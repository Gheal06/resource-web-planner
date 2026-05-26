<?php
require_once "conn.php";
require_once "app/controllers/AuthController.php";
require_once "app/controllers/DashboardController.php";

$controller = new AuthController($connection);
$dashboardController = new DashboardController($connection);
$action = $_GET['action'] ?? '';
$currentUser = $controller->getCurrentUser();

require_once "header.php";
require_once "footer.php";

?>