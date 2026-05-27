<?php
require_once "header.php";

$message = $authController->handleLogin();
$loginAction = 'login.php';
$OTCrequestAction = 'OTCLogin.php';
require_once "app/views/header_view.php";
require_once "app/views/new_inventory_view.php";
require_once "footer.php";
?>