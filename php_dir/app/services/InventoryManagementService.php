<?php
require_once __DIR__ . "/../models/InventoryModel.php";
require_once __DIR__ . "/../models/InventoryPermissionsModel.php";
require_once __DIR__ . "/../models/UserModel.php";

  class InventoryManagementService {
    private $inventoryModel;
    private $inventoryPermissionsModel;
    private $userModel;

    private $readPermissionMask = 1;
    private $insertPermissionMask = 2;
    private $updatePermissionMask = 4;
    private $deletePermissionMask = 8;

    public function __construct($connection) {
      $this->inventoryModel= new InventoryModel($connection);
      $this->inventoryPermissionsModel = new InventoryPermissionsModel($connection);
      $this->userModel = new UserModel($connection);
    }
    public function getUserInventoryIDsByMask($username, $permission_mask) {
        return $this->inventoryPermissionsModel->getUserInventoryIDsByMask($username, $permission_mask);
    }
    public function getUserInventoryById($id) {
        return $this->inventoryModel->getInventoryById($id);
    }
    public function getUserInventoriesByMask($username, $permission_mask) {
        $ids = $this->getUserInventoryIDsByMask($username, $permission_mask);
        $inventories = array();
        foreach ($ids as $id) {
            $inventories[] = $this->inventoryModel->getInventoryById($id);
        }
        return $inventories;
    }

    public function canUserAccessInventory($username, $inventory_id, $permission_mask) {
        return $this->inventoryPermissionsModel->canUserAccessInventory($username, $inventory_id, $permission_mask);
    }
    
    public function canRead($username, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($username, $inventory_id, $this->readPermissionMask);
    }

    public function canInsert($username, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($username, $inventory_id, $this->insertPermissionMask);
    }

    public function canEdit($username, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($username, $inventory_id, $this->updatePermissionMask);
    }

    public function canDelete($username, $inventory_id) {
      return $this->inventoryPermissionsModel->canUserAccessInventory($username, $inventory_id, $this->deletePermissionMask);
    }

    public function createInventory($name, $description, $owner_user_id) {
      // start transaction
      $this->inventoryModel->beginTransaction();
      try {
        $i_id = $this->inventoryModel->create($name, $description, $owner_user_id);
        if ($i_id === false) {
          throw new Exception('Failed to create inventory');
        }

        $permRes = $this->inventoryPermissionsModel->setUserInventoryPermissions($owner_user_id, $i_id, $this->readPermissionMask | $this->insertPermissionMask | $this->updatePermissionMask | $this->deletePermissionMask);
        if ($permRes === false) {
          throw new Exception('Failed to set inventory permissions');
        }

        $this->inventoryModel->commitTransaction();
        return $i_id;
      } catch (Exception $e) {
        $this->inventoryModel->rollbackTransaction();
        return false;
      }
    }
    public function updateInventory($id, $name, $description) {
      return $this->inventoryModel->update($id, $name, $description);
    }
    public function updateInventoryName($id, $name) {

      return $this->inventoryModel->update($id, $name, null);
    }
  }
?>