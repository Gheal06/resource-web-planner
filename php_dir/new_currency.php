<?php
require_once "header.php";

$message = $authController->handleLogin();
$loginAction = 'login.php';
$OTCrequestAction = 'OTCLogin.php';
$createCurrencyAction = 'index.php?action=create-currency';
require_once "app/views/header_view.php";
require_once "app/views/new_currency_view.php";
require_once "footer.php";
?>