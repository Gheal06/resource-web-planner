<?php
require_once __DIR__ . "/../models/UserModel.php";

class AuthService {
    private $userModel;

    public function __construct($connection) {
        $this->userModel = new UserModel($connection);
    }

    public function register($username, $password) {
        if ($this->userModel->findByUsername($username)) {
            return array('success' => false, 'message' => 'Username already exists.');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ok = $this->userModel->create($username, $hash);
        if ($ok) return array('success' => true, 'message' => 'Registration successful.');
        return array('success' => false, 'message' => pg_last_error());
    }

    public function login($username, $password) {
        $user = $this->userModel->findByUsername($username);
        if (!$user) return array('success' => false, 'message' => 'Invalid username or password.');
        if (password_verify($password, $user['password_hash'])) {
            return array('success' => true, 'message' => 'Login successful.');
        }
        return array('success' => false, 'message' => 'Invalid username or password.');
    }
}

?>
