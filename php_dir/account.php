<?php
require_once "header.php";
require_once "app/views/header_view.php";
$message = $authController->handleChangePassword();
$changePasswordAction = 'account.php';
require_once "app/views/account_settings_view.php";
require_once "footer.php";
?>