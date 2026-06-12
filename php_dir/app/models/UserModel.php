<?php
class UserModel {
    private $conn;
    public function __construct($connection) {
        $this->conn = $connection;
    }
    public function getConnection(){
        return $this->conn;
    }
    public function findAll() {
        $res = @pg_query($this->conn, "SELECT id, username, email FROM users");
        if (!$res) return array();
        $users = array();
        while ($row = pg_fetch_assoc($res)) {
            $users[] = $row;
        }
        return $users;
    }
    public function findByUsername($username) {
        $res = @pg_query_params($this->conn, "SELECT * FROM users WHERE username = $1", array($username));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function findByEmail($email) {
        $res = @pg_query_params($this->conn, "SELECT * FROM users WHERE email = $1", array($email));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function findById($id) {
        $res = @pg_query_params($this->conn, "SELECT * FROM users WHERE id = $1", array($id));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function register($username, $email, $password) {
        $res = @pg_query_params($this->conn, "CALL register_user($1, $2, $3)", array($username, $email, $password));
        if ($res === false) {
            $err = pg_last_error($this->conn);
            $lower = strtolower($err);
            if (strpos($lower, 'duplicate') !== false || strpos($lower, 'unique') !== false || strpos($lower, 'already exists') !== false) {
                return array('success' => false, 'message' => 'Username or email already exists.', 'code' => 'duplicate');
            }
            return array('success' => false, 'message' => $err, 'code' => 'db_error');
        }
        return array('success' => true, 'message' => 'Registration successful.');
    }
    public function authenticate_user($username, $password) {
        $res = @pg_query_params($this->conn, "SELECT authenticate_user($1, $2) AS ok", array($username, $password));
        if ($res === false) {
            return array('success' => false, 'message' => pg_last_error($this->conn), 'code' => 'db_error');
        }
        $row = pg_fetch_assoc($res);
        $ok = isset($row['ok']) && $row['ok'] == 't';
        return array('success' => true, 'ok' => $ok);
    }
    public function change_password($username, $password) {
        $res = @pg_query_params($this->conn, "CALL change_user_password($1, $2)", array($username, $password));
        if ($res === false) {
            return array('success' => false, 'message' => pg_last_error($this->conn), 'code' => 'db_error');
        }
        return array('success' => true, 'message' => 'Password updated.');
    }
    public function deleteUser($user_id) {
        $res = @pg_query_params($this->conn, "DELETE FROM users WHERE id = $1", array($user_id));
        if ($res === false) {
            return array('success' => false, 'message' => pg_last_error($this->conn), 'code' => 'db_error');
        }
        if (pg_affected_rows($res) === 0) {
            return array('success' => false, 'message' => 'User not found.', 'code' => 'not_found');
        }
        return array('success' => true, 'message' => 'User deleted.');
    }
}

?>
