<?php
    require_once __DIR__ . "/../services/InventoryManagementService.php";
    class ResourceController{
        private $inventoryManagementService;
        private $connection;
        public function __construct($connection) {
            $this -> connection = $connection;
            $this -> inventoryManagementService = new InventoryManagementService($connection);
        }
        public function addResource($username){
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitNewResource']) && isset($_GET['inventory_id'])){
                $msg = $this -> inventoryManagementService -> addResource($username, $_GET['inventory_id'], $_POST['resource-name'], $_POST['unit'], $_POST['description']);
                if(is_array($msg) && !empty($msg['success'])){
                    header('Location: inventory.php?inventory_id='.$_GET['inventory_id']);
                    return '';
                }
                return is_array($msg) ? ($msg['message'] ?? 'Failed to create resource.') : $msg;
            }
        }
        public function removeResource($username){
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitRemoveResource']) && isset($_GET['inventory_id']) && isset($_GET['resourceId'])){
                $msg = $this -> inventoryManagementService -> removeResource($username, $_GET['inventory_id'], $_GET['resourceId']);
                header('Location: ../inventory.php?inventory_id='.$_GET['inventory_id']);
                return is_array($msg) ? ($msg['message'] ?? '') : $msg;
            }
        }
        public function addFund($username){
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitNewCurrency']) && isset($_GET['inventory_id'])){
                try{
                  $msg = $this->inventoryManagementService->createFonduri($username, $_GET['inventory_id'], $_POST['currencyCode'], $_POST['fundName'], $_POST['description']);
                    // echo $msg['message'];
                    header('Location: inventory.php?inventory_id='.$_GET['inventory_id']);
                    return '';
                }catch(Exception $e){
                    return $e->getMessage();
                }
                return $msg;
            }
        }
        public function removeFund($username){
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitRemoveCurrency']) && isset($_GET['inventory_id']) && isset($_GET['currencyId'])){
                try{
                    $msg = $this->inventoryManagementService->deleteFonduri($username, $_GET['inventory_id'], $_GET['currencyId']);
                }catch(Exception $e){
                }
                finally{
                    header('Location: inventory.php?inventory_id='.$_GET['inventory_id']);
                }
                return $msg;
            }
            header('Location: error.php');
        }
        public function addTag($username){
            if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitNewTag']) && isset($_GET['inventory_id'])){
                $msg = $this -> inventoryManagementService -> addTag($username, $_GET['inventory_id'], $_POST['tag-name'], $_POST['bgcolor'], $_POST['fgcolor']);
                if(is_array($msg) && !empty($msg['success'])){
                    header('Location: inventory.php?inventory_id='.$_GET['inventory_id']);
                    return '';
                }
                return is_array($msg) ? ($msg['message'] ?? 'Failed to create tag.') : $msg;
            }
        }
    }
?>