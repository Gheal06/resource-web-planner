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
$inventoryController->importInventory($username, $_FILES['inventoryFile'] ?? null);
