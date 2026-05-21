<?php
require_once "conn.php";
require_once "app/controllers/AuthController.php";

$controller = new AuthController($connection);
$action = $_GET['action'] ?? '';
$currentUser = $controller->getCurrentUser();

$message = '';
$view = null;

if ($action === 'login') {
    $message = $controller->handleLogin();
    $view = 'app/views/login_view.php';
} elseif ($action === 'register') {
    $message = $controller->handleRegister();
    $view = 'app/views/register_view.php';
} elseif ($action === 'logout') {
    $controller->handleLogout();
} else {
    if ($currentUser) {
        $view = 'app/views/dashboard_view.php';
    }
}

require_once "header.php";

if ($view) {
    require $view;
} else {
    echo '<div class="container"><p>Welcome. Please <a href="index.php?action=login">login</a> or <a href="index.php?action=register">register</a>.</p></div>';
}

require_once "footer.php";

?>