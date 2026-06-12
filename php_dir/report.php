<?php
    require_once "header.php";
    require_once "app/services/InventoryManagementService.php";
    require_once "app/controllers/AuthController.php";
    require_once "app/models/InventoryPermissionsModel.php";
    require_once "app/models/TransactionModel.php";
    function error(){
        header("Location: error.php");
    }
    $authController = new AuthController($connection);
    $inventoryPermissionsModel = new InventoryPermissionsModel($connection);
    $transactionModel = new TransactionModel($connection);
    $inventoryManagementService = new InventoryManagementService($connection);
    $username = $authController -> getCurrentUser();
    $user = $authController -> getUserByUsername($username);
    if(!isset($user)) error();
    $userId = $user['id'];
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $inventoryId = $_POST['inventory_id'];
        if(!isset($userId) || !$inventoryPermissionsModel->canUserAccessInventory($userId, $inventoryId, 1))
            error();
        $resourceId = $_POST['asset_id'];
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        if(!isset($_POST['submit_generate_html']) || !isset($inventoryId) || !isset($resourceId))
            error();
        if(strlen($resourceId)==0) error();
        
        if($resourceId[0]=='f' || resourceId[0]=='r'){ /// funds
            $resourceId = substr($resourceId, 1);
            $history = $transactionModel -> getByResourceId($resourceId);
            require_once "header.php";
            $css = "style/report.css";
            require_once "app/views/html_head_view.php";
            require_once "app/views/report_view.php";
            require_once "footer.php";
        }
        else error();
    }
    else{
        error();
    }
    
?>