<?php
    require_once "conn.php";
    require_once "app/controllers/AuthController.php";
    require_once "app/controllers/InventoryManagementController.php";
    require_once "app/services/GravatarService.php";
    
    $authController = new AuthController($connection);
    $inventoryController = new InventoryManagementController($connection);
    $action = $_GET['action'] ?? '';
    $currentUser = $authController->getCurrentUser();
    $message = '';
    $view = null;
    $inventoryTableIDs = array();
    $css = "index.css";
    if (isset($currentUser) && strtolower($currentUser) == "rares") {
        $css = "rares.css";
    }
    
    $gravatarService = new GravatarService($connection);
    if (!isset($currentUser)) {
        $currentUser = null;
    }
?>
