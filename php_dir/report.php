<?php
    require_once "header.php";
    require_once "app/services/InventoryManagementService.php";
    require_once "app/controllers/AuthController.php";
    require_once "app/models/InventoryPermissionsModel.php";
    require_once "app/models/TransactionModel.php";
    require_once "app/models/ResurseModel.php";
    require_once "app/models/FonduriModel.php";
    function error(){
        header("Location: error.php");
    }
    function errorer(){
      header("Location: errorer.php");
    }
    function str_to_ts($str, $fallback){
        $str = $str ?? '';
        return strlen($str) ? $str : $fallback;
    }
    $authController = new AuthController($connection);
    $inventoryPermissionsModel = new InventoryPermissionsModel($connection);
    $transactionModel = new TransactionModel($connection);
    $inventoryManagementService = new InventoryManagementService($connection);
    $resourceModel = new ResurseModel($connection);
    $fundModel = new FonduriModel($connection);
    $username = $authController -> getCurrentUser();
    $user = $authController -> getUserByUsername($username);
    if(!isset($user)) error();
    $userId = $user['id'];
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $inventoryId = $_POST['inventory_id'];
        if(!isset($userId) || !$inventoryPermissionsModel->canUserAccessInventory($userId, $inventoryId, 1))
            error();
        $resourceId = $_POST['asset_id'];
        $startDate = str_to_ts($_POST['start_date'], '-infinity');
        $endDate = str_to_ts($_POST['end_date'], 'infinity');
        if($endDate!='infinity')
            $endDate=date('Y-m-d', strtotime($endDate.' +1 day'));
        if(!isset($inventoryId) || !isset($resourceId))
            error();
        if(strlen($resourceId)==0) error();
        $tp = $resourceId[0];
        if($tp=='f' || $tp=='r'){ /// funds
            if($tp=='f'){
                $resourceId = substr($resourceId, 1);
                $resource = $fundModel -> getById($resourceId);
                $history = $inventoryManagementService -> statisticiFond($inventoryId, $resourceId, $startDate, $endDate);
                $startingBalance = $inventoryManagementService -> fundWayback($resourceId, $startDate);
            }
            else{
                $resourceId = substr($resourceId, 1);
                $resource = $resourceModel -> getResurseById($resourceId);
                $history = $inventoryManagementService -> statisticiResursa($inventoryId, $resourceId, $startDate, $endDate);
                //echo "here".$history;
                $startingBalance = $inventoryManagementService -> resourceWayback($resourceId, $startDate);
            }
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