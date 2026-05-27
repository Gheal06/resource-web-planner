<?php

require_once "header.php";
require_once "app/controllers/InventoryManagementController.php";
$inventoryController = new InventoryManagementController($connection);
$inventoryId = $_GET['inventory_id'] ?? null;
$inventory = $inventoryId ? $inventoryController->getUserInventoryById($inventoryId) : null;

require_once "app/views/header_view.php";
require_once "app/views/inventory_view.php";

?>