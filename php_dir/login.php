<?php
require_once "header.php";

$message = $controller->handleLogin();
$loginAction = 'login.php';
require_once "app/views/header_view.php";
require_once "app/views/login_view.php";
require_once "footer.php";
?>