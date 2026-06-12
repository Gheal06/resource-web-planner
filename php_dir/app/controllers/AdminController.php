<?php
require_once __DIR__ . "/../services/AdminService.php";

class AdminController {
    private $adminService;

    public function __construct($connection) {
        $this->adminService = new AdminService($connection);
    }

    public function adminAction() {
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          if (isset($_POST['reset-db'])) {
              return $this->adminService->resetDatabase();
          }
          elseif (isset($_POST['reset-functions'])) {
              return $this->adminService->resetFunctions();
          }
          elseif (isset($_POST['submit-delete-user'])) {
              $user_id = $_POST['user_id'] ?? '';
              return $this->adminService->deleteUser($user_id);
          } elseif (isset($_POST['submit-update-role'])) {
              $user_id = $_POST['user_id'] ?? '';
              $is_admin = isset($_POST['is-admin']);
              if ($is_admin) {
                  return $this->adminService->addAdmin($user_id);
              } else {
                  return $this->adminService->deleteAdmin($user_id);
              }
          }
          elseif (isset($_POST['submit-send-email'])) {
              $user_id = $_POST['user_id'] ?? '';
              $subject = $_POST['email_subject'] ?? 'Default Subject';
              $message = $_POST['email_message'] ?? 'Default Message';
              return $this->adminService->sendEmailToUser($user_id, $subject, $message);
          } 
          elseif (isset($_POST['submit-send-notification'])) {
              $user_id = $_POST['user_id'] ?? '';
              $subject = $_POST['notification_subject'] ?? 'Default Subject';
              $message = $_POST['notification_message'] ?? 'Default Message';
              return $this->adminService->sendNotificationToUser($user_id, $subject, $message);
          }
      }
    }

    public function sendEmail() {
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-email'])) {
          $user_id = $_POST['user_id'] ?? '';
          $subject = $_POST['email_subject'] ?? '';
          $message = $_POST['email_message'] ?? '';
          return $this->adminService->sendEmailToUser($user_id, $subject, $message);
      }
    }

    public function sendNotification() {
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-notification'])) {
          $user_id = $_POST['user_id'] ?? '';
          $subject = $_POST['notification_subject'] ?? '';
          $message = $_POST['notification_message'] ?? '';
          return $this->adminService->sendNotificationToUser($user_id, $subject, $message);
      }
    }
}

?>
