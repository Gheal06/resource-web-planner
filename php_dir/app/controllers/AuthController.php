<?php
require_once __DIR__ . "/../services/AuthService.php";

class AuthController {
    private $authService;
    private $cookieName = 'auth_token';

    public function __construct($connection) {
        $this->authService = new AuthService($connection);
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $result = $this->authService->login($username, $password);
            if ($result['success']) {
                $this->setAuthCookie($result['token']);
                header('Location: index.php');
                exit();
            }
            return $result['message'];
        }
        return '';
    }

    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $repeated_password = $_POST['repeat-password'] ?? '';
            if ($password !== $repeated_password) {
                return 'Passwords do not match.';
            }
            $email    = $_POST['email'] ?? '';
            $result = $this->authService->register($username, $email, $password);
            if ($result['success']) {
                $this->setAuthCookie($result['token']);
                header('Location: index.php');
                exit();
            }
            return $result['message'];
        }
        return '';
    }

    public function handleChangePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change-password'])) {
            $currentUser = $this->getCurrentUser();
            if (!$currentUser) {
                return 'You must be logged in to change your password.';
            }
            $new_password = $_POST['new-password'] ?? '';
            $repeated_password = $_POST['repeat-password'] ?? '';
            if ($new_password !== $repeated_password) {
                return 'Passwords do not match.';
            }
            $result = $this->authService->change_password($currentUser, $new_password);
            if ($result['success']) {
                return 'Password changed successfully.';
            }
            return $result['message'];
        }
        return '';
    }

    public function handleOTCLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['OTC_login'])) {
            $username = $_POST['username'] ?? '';
            $code = $_POST['OTC'] ?? '';
            $result = $this->authService->login_with_OTC($username, $code);
            if ($result['success']) {
                $this->setAuthCookie($result['token']);
                header('Location: index.php');
                exit();
            }
            return $result['message'];
        }
        return '';
    }

    public function sendOTC() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['OTC_request'])) {
            $email = $_POST['email'] ?? '';
            $result = $this->authService->send_OTC_to_email($email);
            if ($result['success']) {
              header('Location: OTCLogin.php?action=OTClogin');
              exit();
            }
            return $result['message'];
        }
        return '';
    }

    public function handleLogout() {
        $this->clearAuthCookie();
        header('Location: index.php');
        exit();
    }

    public function getCurrentUser() {
        return $this->authService->getCurrentUserFromToken($_COOKIE[$this->cookieName] ?? null);
    }

    private function setAuthCookie($token) {
        setcookie($this->cookieName, $token, array(
            'expires' => time() + 60 * 60 * 24,
            'path' => '/',
            'httponly' => true,
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'samesite' => 'Lax'
        ));
        $_COOKIE[$this->cookieName] = $token;
    }

    private function clearAuthCookie() {
        setcookie($this->cookieName, '', array(
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'samesite' => 'Lax'
        ));
        unset($_COOKIE[$this->cookieName]);
    }
}

?>
