<?php
require_once "conn.php";
require_once "app/controllers/AuthController.php";

$controller = new AuthController($connection);
$controller->handleLogout();

?>