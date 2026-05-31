<?php
require_once "../conn.php";
require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/InventoryManagementController.php";

$controller = new AuthController($connection);
$username = $controller->getCurrentUser();
if (!isset($username)) {
    header("Location: ../error.php");
    exit();
}

$inventoryController = new InventoryManagementController($connection);
$inventoryId = $_GET['inventory_id'] ?? null;
$exportType = $_POST['type'] ?? null;
$inventoryController->exportInventory($username, $inventoryId, $exportType);
