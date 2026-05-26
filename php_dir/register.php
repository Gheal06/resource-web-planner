<?php
require_once "header.php";

$message = $controller->handleRegister();
$registerAction = 'register.php';
require_once "app/views/header_view.php";
require_once "app/views/register_view.php";
require_once "footer.php";
?>