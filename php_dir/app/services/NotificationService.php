<?php
require_once __DIR__ . "/../models/NotificationModel.php";
require_once __DIR__ . "/../models/AdminModel.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/MailingService.php";
class NotificationService {
  private $notificationModel;
  private $mailingService;
  private $userModel;
  public function __construct($connection) {
    $this->notificationModel = new NotificationModel($connection);
    $this->mailingService = new MailingService();
    $this->userModel = new UserModel($connection);
  }

  public function createNotification($user_id, $inventory_id, $subject, $body) {
    $this->notificationModel->createNotification($user_id, $inventory_id, $subject, $body);
    $user = $this->userModel->findById($user_id);
    if ($user && isset($user['email'])) {
      $this->mailingService->send_email($user['email'], $subject, $body);
    }
  }

  public function getNotificationsForUserID($user_id) {
    return $this->notificationModel->getNotificationsForUser($user_id);
  }
  public function getNotificationsForUsername($username) {
    $user = $this->userModel->findByUsername($username);
    if (!$user) return array();
    return $this->getNotificationsForUserID($user['id']);
  }

  public function deleteNotification($notification_id, $user_id) {
    return $this->notificationModel->deleteNotification($notification_id, $user_id);
  }
}
