<?php
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/JwtService.php";

class AuthService {
    private $userModel;
    private $jwtService;

    public function __construct($connection) {
        $this->userModel = new UserModel($connection);
        $this->jwtService = new JwtService();
    }

    public function register($username, $email, $password) {
        if ($this->userModel->findByUsername($username)) {
            return array('success' => false, 'message' => 'Username already exists.');
        }
        if ($this->userModel->findByEmail($email)) {
            return array('success' => false, 'message' => 'This email is already in use.');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ok = $this->userModel->create($username, $email, $hash);
        if ($ok) {
            return array('success' => true, 'message' => 'Registration successful.');
        }
        return array('success' => false, 'message' => pg_last_error());
    }

    public function login($username, $password) {
        $user = $this->userModel->findByUsername($username);
        if (!$user) return array('success' => false, 'message' => 'Invalid username or password.');
        if (password_verify($password, $user['password_hash'])) {
            $token = $this->jwtService->encode(array(
                'sub' => $user['username'],
                'iat' => time(),
                'exp' => time() + 60 * 60 * 24
            ));
            return array('success' => true, 'message' => 'Login successful.', 'token' => $token, 'user' => $user['username']);
        }
        return array('success' => false, 'message' => 'Invalid username or password.');
    }

    public function createTokenForUser($username) {
        return $this->jwtService->encode(array(
            'sub' => $username,
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24
        ));
    }

    public function getCurrentUserFromToken($token) {
        if (!$token) {
            return null;
        }

        $payload = $this->jwtService->decode($token);
        if (!$payload || empty($payload['sub'])) {
            return null;
        }

        return $payload['sub'];
    }
}

?>
