<?php
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/JwtService.php";
require_once __DIR__ . "/MailingService.php";
require_once __DIR__ . "/../models/PasswordRecoveryTokenModel.php";

class AuthService {
    private $userModel;
    private $jwtService;
    private $mailingService;
    private $passwordRecoveryTokenModel;

    public function __construct($connection) {
        $this->userModel = new UserModel($connection);
        $this->jwtService = new JwtService();
        $this->mailingService = new MailingService();
        $this->passwordRecoveryTokenModel = new PasswordRecoveryTokenModel($connection);
    }

    public function register($username, $email, $password) {
        if ($this->userModel->register($username, $email, $password)) {
            $token = $this->create_token_for_user($username);            
            return array('success' => true, 'message' => 'Registration successful.', 'token' => $token, 'user' => $username);
        }
        return array('success' => false, 'message' => 'Registration failed.');
    }

    public function login($username, $password) {
        // echo $this->userModel->authenticate_user($username, $password);
        if ($this->userModel->authenticate_user($username, $password)) {
            $token = $this->create_token_for_user($username);
            return array('success' => true, 'message' => 'Login successful.', 'token' => $token, 'user' => $username);
        }
        return array('success' => false, 'message' => 'Invalid username or password');
    }

    public function login_with_OTC($username, $code){
        $user = $this->userModel->findByUsername($username);
        if (!$user) return array('success' => false, 'message' => 'Invalid username or code.');
        $tokenData = $this->passwordRecoveryTokenModel->findByUsername($user['id']);
        if (!$tokenData || $tokenData['code'] !== $code){
            return array('success' => false, 'message' => 'Invalid username or code.');
        }
        if (strtotime($tokenData['expires_at']) < time()) {
            return array('success' => false, 'message' => 'This code has expired. Please request a new one.');
        }
        $this->passwordRecoveryTokenModel->create($user['id'], '', date('Y-m-d H:i:s')); // șterge codul
        $token = $this->create_token_for_user($user['username']);
        $this->passwordRecoveryTokenModel->delete($user['id']);
        return array('success' => true, 'message' => 'Login successful.', 'token' => $token, 'user' => $user['username']);
    }   

    public function change_password($username, $password) {
        if ($this->userModel->change_password($username, $password)) {
            return array('success' => true, 'message' => 'Password changed successfully.');
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
        return $this->passwordRecoveryTokenModel->create($user['id'], $code, $expires_at) 
            ? array('success' => true, 'code' => $code) 
            : array('success' => false, 'message' => 'Failed to create recovery code.');
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
        if (!$user) return array('success' => false, 'message' => 'No account with this email.');
        $codeData = $this->create_password_recovery_code($user['username']);
        if (!$codeData['success']) {
            return array('success' => false, 'message' => 'Failed to create recovery code.');
        }
        $code = $codeData['code'];
        return $this->mailingService->send_OTC($user['email'], $code);
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
