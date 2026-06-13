<?php

require_once "header.php";
require_once "app/controllers/InventoryManagementController.php";
require_once "app/services/InventoryManagementService.php";

$inventoryService = new InventoryManagementService($connection);
$inventoryController = new InventoryManagementController($connection);
$inventoryId = $_GET['inventory_id'] ?? null;
$inventory = $inventoryId ? $inventoryController->getUserInventoryById($inventoryId) : null;

verifyAccess($inventoryId, READ);

require_once "app/views/header_view.php";

if(!isset($inventory) || !isset($inventory['id'])){
    require_once "app/views/error_view.php";
}
else{
    
    $resourceModel = new ResurseModel($connection);
    $tags = $resourceModel -> getAllTags($inventoryId);
    $selectedTagId = $_GET['tag_id'] ?? null;

    // Get owner info
    $owner = $authController->getUserById($inventory['owner_id']);
    $inventoryOwnerUsername = $owner ? $owner['username'] : 'Unknown';

    require_once "app/views/inventory_view.php";
}
?>