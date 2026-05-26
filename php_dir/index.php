<?php

require_once "header.php";

if ($action === 'login') {
    $message = $controller->handleLogin();
    $view = 'app/views/login_view.php';
    $formAction = 'index.php?action=login';
} elseif ($action === 'register') {
    $message = $controller->handleRegister();
    $view = 'app/views/register_view.php';
} elseif ($action === 'logout') {
    $controller->handleLogout();
} else {
    if ($currentUser) {
        $inventoryTableIDs = $inventoryController->getUserReadableInventoryTables($currentUser);
        $view = 'app/views/dashboard_view.php';
    }
}

require_once __DIR__ . '/app/views/header_view.php';

if ($view) {
    require $view;
} else {
    echo '<div class="container"><p>Welcome. Please <a href="index.php?action=login">login</a> or <a href="index.php?action=register">register</a>.</p></div>';
}

require_once "footer.php";

?>