<?php
class UserModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function findByUsername($username) {
        $res = pg_query_params($this->conn, "SELECT * FROM user_table WHERE username = $1", array($username));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function findByEmail($email) {
        $res = pg_query_params($this->conn, "SELECT * FROM user_table WHERE email = $1", array($email));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function create($username, $email, $password_hash) {
        return pg_query_params($this->conn, "INSERT INTO user_table (username, email, password_hash) VALUES ($1, $2, $3)", array($username, $email, $password_hash));
    }
    public function update_password($username, $password_hash) {
        return pg_query_params($this->conn, "UPDATE user_table SET password_hash = $1 WHERE username = $2", array($password_hash, $username));
    }
}

?>
