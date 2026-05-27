<?php

require_once "header.php";
require_once "app/controllers/InventoryManagementController.php";
require_once "app/views/header_view.php";

$inventoryController = new InventoryManagementController($connection);
$inventoryId = $_GET['inventory_id'] ?? null;
$inventory = $inventoryId ? $inventoryController->getUserInventoryById($inventoryId) : null;
if(!isset($inventory)){
    require_once "app/views/error_view.php";
}
else{
    $resourceModel = new ResurseModel($connection);
    $tags = $resourceModel -> getAllTags($inventoryId);

    require_once "app/views/inventory_view.php";
}
?>