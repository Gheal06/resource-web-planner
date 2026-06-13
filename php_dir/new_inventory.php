<?php
require_once "header.php";
require_once "app/services/InventoryManagementService.php";
$createInventoryAction = 'new_inventory.php';
$OTCrequestAction = 'OTCLogin.php';
$inventoryController = new InventoryManagementController($connection);
$authController = new AuthController($connection);
$message = $inventoryController -> createInventory($authController -> getCurrentUser()); 
require_once "app/views/header_view.php";
require_once "app/views/new_inventory_view.php";
require_once "footer.php";
?>