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
  }
?>