<?php
require_once "header.php";

$message = $authController->handleLogin();
$loginAction = 'login.php';
$OTCrequestAction = 'OTCLogin.php';
$createResourceAction = 'index.php?action=create-resource';
require_once "app/views/header_view.php";
require_once "app/views/new_resource_view.php";
require_once "footer.php";
?>