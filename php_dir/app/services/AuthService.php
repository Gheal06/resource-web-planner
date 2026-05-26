<?php
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/JwtService.php";
require_once __DIR__ . "/MailingService.php";

class AuthService {
    private $userModel;
    private $jwtService;
    private $mailingService;

    public function __construct($connection) {
        $this->userModel = new UserModel($connection);
        $this->jwtService = new JwtService();
        $this->mailingService = new MailingService();
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
            $token = $this->create_token_for_user($username);
            return array('success' => true, 'message' => 'Login successful.', 'token' => $token, 'user' => $user['username']);
        }
        return array('success' => false, 'message' => 'Invalid username or password.');
    }

    public function login_with_OTC($username, $code){
        $user = $this->userModel->findByUsername($username);
        if (!$user) return array('success' => false, 'message' => 'Invalid username or code.');
        $tokenData = $this->passwordRecoveryTokenModel->findByUsername($user['id']);
        if (!$tokenData || $tokenData['code'] !== $code || strtotime($tokenData['expires_at']) < time()) {
            return array('success' => false, 'message' => 'Invalid username or code.');
        }
        $this->passwordRecoveryTokenModel->create($user['id'], '', date('Y-m-d H:i:s')); // șterge codul
        $token = $this->create_token_for_user($user['username']);
        return array('success' => true, 'message' => 'Login successful.', 'token' => $token, 'user' => $user['username']);
    }   

    public function reset_password($username, $password) {
        $user = $this->userModel->findByUsername($username);
        if (!$user) return array('success' => false, 'message' => 'User not found.');
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $res = $this->userModel->update_password($username, $hash);
        if ($res) {
            return array('success' => true, 'message' => 'Password reset successful.');
        }
        return array('success' => false, 'message' => pg_last_error());
    }

    public function create_password_recovery_code($username) {
        $user = $this->userModel->findByUsername($username);
        if (!$user) return array('success' => false, 'message' => 'User not found.');
        $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < 32; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        $expires_at = date('Y-m-d H:i:s', time() + 60 * 5); // expira peste 5 minute
        $res = pg_query_params($this->userModel->conn, "INSERT INTO password_recovery_codes (user_id, code, expires_at) VALUES ($1, $2, $3)", array($user['id'], $code, $expires_at));
        if ($res) {
            return array('success' => true, 'message' => 'Recovery code created.', 'code' => $code);
        }
        return array('success' => false, 'message' => pg_last_error());
    }

    public function send_OTC_to_username($username){
        $user = $this->userModel->findByUsername($username);
        if (!$user) return array('success' => false, 'message' => 'User not found.');
        $codeData = $this->create_password_recovery_code($username);
        if (!$codeData['success']) {
            return array('success' => false, 'message' => 'Failed to create recovery code.');
        }
        $code = $codeData['code'];
        $this->mailingService->send_OTC($user['email'], $code);
        return array('success' => true, 'message' => 'Recovery code sent.');

    }

    public function send_OTC_to_email($user_email){
        $user = $this->userModel->findByEmail($user_email);
        if (!$user) return array('success' => false, 'message' => 'User not found.');
        $codeData = $this->create_password_recovery_code($user['username']);
        if (!$codeData['success']) {
            return array('success' => false, 'message' => 'Failed to create recovery code.');
        }
        $code = $codeData['code'];
        $this->mailingService->send_OTC($user['email'], $code);
        return array('success' => true, 'message' => 'Recovery code sent.');
    }

    public function create_token_for_user($username) {
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
