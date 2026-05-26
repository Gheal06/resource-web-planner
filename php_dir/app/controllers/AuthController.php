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
            $email    = $_POST['email'] ?? '';
            $result = $this->authService->register($username, $email, $password);
            if ($result['success']) {
                $this->setAuthCookie($this->authService->create_token_for_user($username));
                header('Location: index.php');
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
