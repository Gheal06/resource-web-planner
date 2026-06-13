<?php
    require_once "conn.php";
    require_once "app/controllers/AuthController.php";
    require_once "app/services/AuthService.php";
    require_once "app/controllers/InventoryManagementController.php";
    require_once "app/services/InventoryManagementService.php";
    require_once "app/services/GravatarService.php";
    
    $authController = new AuthController($connection);
    $authService = new AuthService($connection);
    $inventoryController = new InventoryManagementController($connection);
    $inventoryService = new InventoryManagementService($connection);
    $inventoryId = $_GET['inventory_id'] ?? null;
    
    $action = $_GET['action'] ?? '';
    $currentUser = $authController->getCurrentUser();
    $currentUserId = $authService->getUserByUsername($currentUser);
    if(isset($currentUserId))
        $currentUserId = $currentUserId['id'];
    define("READ", $inventoryService->readPermissionMask);
    define("EDIT", $inventoryService->editPermissionMask);
    define("UPDATE", $inventoryService->updatePermissionMask);
    define("DELETE", $inventoryService->deletePermissionMask);
    function verifyAccess($inventoryId, $msk){
        global $inventoryService;
        global $currentUserId;
        if(!isset($currentUserId) || !isset($inventoryId))
            header("Location: error.php");
        if(!$inventoryService -> canUserAccessInventory($currentUserId, $inventoryId, $msk))
            header("Location: error.php");
    }
    $message = '';
    $view = null;
    $inventoryTableIDs = array();
    $css = "style/index.css";
    if (isset($currentUser) && strtolower($currentUser) == "rares") {
        $css = "rares.css";
    }
    
    $gravatarService = new GravatarService($connection);
    if (!isset($currentUser)) {
        $currentUser = null;
    }
?>
