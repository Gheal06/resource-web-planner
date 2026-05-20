<?php
require_once __DIR__ . "/../services/AuthService.php";

class AuthController {
    private $authService;

    public function __construct($connection) {
        $this->authService = new AuthService($connection);
    }

    public function handleLogin() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $result = $this->authService->login($username, $password);
            if ($result['success']) {
                $_SESSION['username'] = $username;
                header('Location: index.php');
                exit();
            }
            return $result['message'];
        }
        return '';
    }

    public function handleRegister() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $result = $this->authService->register($username, $password);
            if ($result['success']) {
                $_SESSION['username'] = $username;
                header('Location: index.php');
                exit();
            }
            return $result['message'];
        }
        return '';
    }

    public function handleLogout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = array();
        session_destroy();
        header('Location: index.php');
        exit();
    }
}

?>
