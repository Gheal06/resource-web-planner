<?php

class NotificationModel {
  private $conn;
  public function __construct($connection) {
    $this->conn = $connection;
  } 
  public function createNotification($user_id, $inventory_id, $title, $message) {
    $res = @pg_query_params($this->conn, "INSERT INTO notifications (user_id, inventory_id, title, message) VALUES ($1, $2, $3, $4)", array($user_id, $inventory_id, $title, $message));
    return $res !== false;
  }
  public function getNotificationsForUser($user_id) {
    $res = @pg_query_params($this->conn, "SELECT id, inventory_id, title, message, created_at FROM notifications WHERE user_id = $1 ORDER BY created_at DESC", array($user_id));
    if (!$res) return array();
    $notifications = array();
    while ($row = pg_fetch_assoc($res)) {
      $notifications[] = $row;
    }
    return $notifications;
  }
  public function deleteNotification($notification_id, $user_id) {
    $res = @pg_query_params($this->conn, "DELETE FROM notifications WHERE id = $1 AND user_id = $2", array($notification_id, $user_id));
    return $res !== false && pg_affected_rows($res) > 0;
  }
}

?>