<?php
require_once "header.php";

$message = '';


$controller = new AuthController($connection);
if ($action == 'OTClogin') {
  $OTCLoginAction = 'OTCLogin.php?action=OTClogin';
  $message = $controller->handleOTCLogin();
  $view = 'app/views/login_with_OTC_view.php';
}
else{
  $OTCrequestAction = 'OTCLogin.php';
  $message = $controller->sendOTC();
  $view = 'app/views/request_OTC_view.php';
}
require_once "app/views/header_view.php";
require_once $view;

require_once "footer.php";
?>