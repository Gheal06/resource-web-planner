<?php
require_once __DIR__ . "/../models/AdminModel.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/MailingService.php";
require_once __DIR__ . "/NotificationService.php";
require_once __DIR__ . "/../controllers/AuthController.php";
class AdminService {
    private $adminModel;
    private $userModel;
    private $mailingService;
    private $notificationService;
    private $authController;

    public function __construct($connection) {
        $this->adminModel = new AdminModel($connection);
        $this->userModel = new UserModel($connection);
        $this->notificationService = new NotificationService($connection);
        $this->mailingService = new MailingService();
        $this->authController = new AuthController($connection);
    }
    
    public function sendEmailToUser($user_id, $subject, $message) {
        $user = $this->userModel->findById($user_id);
        if (!$user || !isset($user['email'])) {
            return array('success' => false, 'message' => 'User not found or has no email.', 'code' => 'not_found');
        }
        $this->mailingService->send_email($user['email'], $subject, $message);
        return array('success' => true, 'message' => 'Email sent to ' . $user['email']);
    }

    public function sendNotificationToUser($user_id, $subject, $message) {
        $user = $this->userModel->findById($user_id);
        if (!$user) {
            return array('success' => false, 'message' => 'User not found.', 'code' => 'not_found');
        }
        $this->notificationService->createNotification($user_id, null, $subject, $message);
        return array('success' => true, 'message' => 'Notification sent to user ID ' . $user_id);
    }
    public function getAllAdmins() {
        return $this->adminModel->getAllAdmins();
    }

    public function getAllUsers() {
        return $this->userModel->findAll();
    }

    public function deleteUser($user_id) {
      return $this->userModel->deleteUser($user_id);
    }
    public function deleteAdmin($user_id) {
        $res = $this->adminModel->deleteAdmin($user_id);
        if ($res['success'] == false){
          return $res;
        }
        $who = $this->authController->getCurrentUser();
        $currentUser = $who ? $who : 'Unknown';
        $this->sendNotificationToUser($user_id, "Admin Role Revoked", "Your admin privileges have been revoked by $currentUser. R.I.P");
        return $res;
    }

    public function addAdmin($user_id) {
      $res = $this->adminModel->addAdmin($user_id);
      if ($res['success'] == false){
        return $res;
      }
      $who = $this->authController->getCurrentUser();
      $currentUser = $who ? $who : 'Unknown';
      $this->sendNotificationToUser($user_id, "Admin Role Granted", "You have been granted admin privileges by $currentUser. Enjoy.");
      return $res;
    }


    public function resetDatabase() {
      $oldpath = getcwd();
      chdir('/var/www/html/admin_scripts');
      $message = shell_exec('bash initdb.sh defarg 2>&1');
      chdir($oldpath);
      return array('success' => true, 'message' => $message);
    }
    public function resetFunctions() {
      $oldpath = getcwd();
      chdir('/var/www/html/admin_scripts');
      $message = shell_exec('bash initdb.sh 2>&1');
      chdir($oldpath);
      return array('success' => true, 'message' => $message);
    }
}

?>