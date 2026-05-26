<?php
    require_once "conn.php";
    require_once "app/controllers/AuthController.php";
    require_once "app/controllers/InventoryController.php";
    require_once "app/services/GravatarService.php";
    
    $controller = new AuthController($connection);
    $inventoryController = new InventoryController($connection);
    $action = $_GET['action'] ?? '';
    $currentUser = $controller->getCurrentUser();
    $message = '';
    $view = null;
    $inventoryTableIDs = array();
    
    $gravatarService = new GravatarService($connection);
    if (!isset($currentUser)) {
        $currentUser = null;
    }
?>
