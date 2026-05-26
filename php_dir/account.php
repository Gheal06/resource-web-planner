<?php
require_once "conn.php";
require_once "app/controllers/AuthController.php";
require_once "app/controllers/InventoryController.php";

$controller = new AuthController($connection);
$inventoryController = new InventoryController($connection);
$action = $_GET['action'] ?? '';
$currentUser = $controller->getCurrentUser();

require_once "header.php";
require_once "footer.php";

?>