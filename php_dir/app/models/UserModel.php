<?php
class UserModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function findByUsername($username) {
        $res = pg_query_params($this->conn, "SELECT * FROM user_table WHERE user_name = $1", array($username));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function create($username, $password_hash) {
        return pg_query_params($this->conn, "INSERT INTO user_table (user_name, password_hash) VALUES ($1, $2)", array($username, $password_hash));
    }
}

?>
