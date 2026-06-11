<?php
class AdminModel {
  private $conn;
  
  public function __construct($connection) {
    $this->conn = $connection;
  }

  public function getAllAdmins() {
    $res = @pg_query($this->conn, "SELECT id, username, email FROM users WHERE is_admin = true");
    if (!$res) return null;
    $admins = [];
    while ($row = pg_fetch_assoc($res)) {
      $admins[] = $row;
    }
    return $admins;
  }

  public function deleteAdmin($user_id) {
    $res = @pg_query_params($this->conn, "DELETE FROM admins WHERE user_id = $1", array($user_id));
    if ($res === false) {
      return array('success' => false, 'message' => pg_last_error($this->conn), 'code' => 'db_error');
    }
    if (pg_affected_rows($res) === 0) {
      return array('success' => false, 'message' => 'Admin not found or cannot delete self.', 'code' => 'not_found');
    }
    return array('success' => true, 'message' => 'Admin deleted.');
  }

  public function addAdmin($user_id) {
    $res = @pg_query_params($this->conn, "INSERT INTO admins (user_id) VALUES ($1)", array($user_id));
    if ($res === false) {
      $err = pg_last_error($this->conn);
      $lower = strtolower($err);
      if (strpos($lower, 'duplicate') !== false || strpos($lower, 'unique') !== false || strpos($lower, 'already exists') !== false) {
        return array('success' => false, 'message' => 'Admin already exists.', 'code' => 'duplicate');
      }
      return array('success' => false, 'message' => $err, 'code' => 'db_error');
    }
    return array('success' => true, 'message' => 'Admin added successfully.');
  }

  public function isAdmin($user_id) {
    $res = @pg_query_params($this->conn, "SELECT 1 FROM admins WHERE user_id = $1", array($user_id));
    if (!$res) return false;
    return pg_num_rows($res) > 0;
  }
}
?>