<?php
class PasswordRecoveryTokenModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function findByUsername($userID) {
        $res = pg_query_params($this->conn, "SELECT * FROM password_recovery_codes WHERE user_id = $1", array($userID));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function create($userID, $code, $expires_at) {
        $res = pg_query_params($this->conn, "DELETE FROM password_recovery_codes WHERE user_id = $1", array($userID));
        return pg_query_params($this->conn, "INSERT INTO password_recovery_codes (user_id, code, expires_at) VALUES ($1, $2, $3)", array($userID, $code, $expires_at));
    }
}

?>
