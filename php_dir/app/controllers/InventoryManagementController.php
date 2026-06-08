<?php
require_once __DIR__ . "/../services/InventoryManagementService.php";

class InventoryManagementController {
    private $inventoryManagementService;
    private $connection;
    public function __construct($connection) {
        $this->connection = $connection;
        $this->inventoryManagementService = new InventoryManagementService($connection);
    }
    
    public function getUserReadableInventoryIDs($username) {
        return $this->inventoryManagementService->getUserInventoryIDsByMask($username, 1);
    }
    public function getUserReadableInventories($username) {
        return $this->inventoryManagementService->getUserInventoriesByMask($username, 1);
    }

    
    public function getUserInventoryIDsByMask($username, $permission_mask) {
        return $this->inventoryManagementService->getUserInventoryIDsByMask($username, $permission_mask);
    }
    public function getUserInventoriesByMask($username, $permission_mask) {
        return $this->inventoryManagementService->getUserInventoriesByMask($username, $permission_mask);
    }
    public function getUserInventoryById($id) {
        return $this->inventoryManagementService->getUserInventoryById($id);
    }
    public function getFonduriForInventory($username, $inventory_id) {
        $user = $this->inventoryManagementService->getUserByUsername($username);
        if (!$user || !isset($user['id'])) {
          return array();
        }
        if (!$this->inventoryManagementService->canRead($user['id'], $inventory_id)) {
            return array();
        }
        return $this->inventoryManagementService->getFonduriByInventoryId($inventory_id);
    }

    public function getResourcesForInventory($username, $inventory_id, $tag_id = null) {
        $user = $this->inventoryManagementService->getUserByUsername($username);
        if (!$user || !isset($user['id'])) {
          return array();
        }
        if (!$this->inventoryManagementService->canRead($user['id'], $inventory_id)) {
            return array();
        }
        if ($tag_id !== null && trim((string)$tag_id) !== '') {
            return $this->inventoryManagementService->getResourcesByTags($username, $inventory_id, array($tag_id));
        }
        return $this->inventoryManagementService->getResourcesByInventoryId($inventory_id);
    }
    public function createInventory($username) {
        if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['submitNewInventory']) && isset($username)){
            try{
                $retval = $this->inventoryManagementService->createInventory($_POST['inventoryName'], $_POST['description'], $username);
                header("Location: inventory.php?inventory_id=".$retval);
            }catch(Exception $e){
                return $e->getMessage();
            }
            return "";
        }
    }
    public function exportInventory($username, $inventory_id, $type){
        if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($inventory_id) || !isset($type)){
            header('Location: ../error.php');
            exit();
        }

        try{
            $export = $this->inventoryManagementService->exportInventory($username, $inventory_id, $type);
            if (!isset($export['success']) || !$export['success']) {
                header('Location: ../error.php');
                exit();
            }

            header('Content-Type: ' . $export['mime']);
            header('Content-Disposition: attachment; filename="' . $export['filename'] . '"');
            echo $export['content'];
            exit();
        }catch(Exception $e){
            header('Location: ../error.php');
            exit();
        }
    }
    public function importInventory($username, $uploaded_file){
        if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($uploaded_file)){
            header('Location: ../error.php');
            echo "trollezi";
            exit();
        }

        try{
            $import = $this->inventoryManagementService->importInventory($username, $uploaded_file);
            if (!isset($import['success']) || !$import['success']) {
                header('Location: ../error.php');
                echo "milsugi";
                exit();
            }

            header('Location: ../inventory.php?inventory_id=' . urlencode($import['inventory_id']));
            echo "yoloo";
            exit();
        }catch(Exception $e){
            header('Location: ../error.php');
            echo "pulamea";
            exit();
        }
    }
    public function removeInventory($username){
        if($_SERVER['REQUEST_METHOD']=='POST' && isset($_GET["inventory_id"])){
            $msg = '';
            try{
                $this->inventoryManagementService -> deleteInventory($username, $_GET['inventory_id']);
            }catch(Exception $e){
                echo $e;
            }
            finally{
                header('Location: ../index.php');
                exit();
            }
        }
        header('Location: ../error.php');
    }

    public function getManageAccessData($username, $inventory_id) {
        return $this->inventoryManagementService->getAccessManagementData($username, $inventory_id);
    }

    public function updateUserAccess($username, $inventory_id, $target_user_id, $new_permissions) {
        return $this->inventoryManagementService->updateUserInventoryAccess($username, $inventory_id, $target_user_id, $new_permissions);
    }

    public function removeUserAccess($username, $inventory_id, $target_user_id) {
        return $this->inventoryManagementService->removeUserInventoryAccess($username, $inventory_id, $target_user_id);
    }

    public function addUserAccess($username, $inventory_id, $target_username, $initial_permissions = 1) {
        return $this->inventoryManagementService->addUserInventoryAccess($username, $inventory_id, $target_username, $initial_permissions);
    }
}
?>
