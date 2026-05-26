<?php
require_once __DIR__ . "/../models/InventoryModel.php";
  class InventoryManagementService {
    private $inventoryModel;

    public function __construct($connection) {
      $this->inventoryModel= new InventoryModel($connection);
    }
    public function getUserInventoryIDsByMask($username, $permission_mask) {
        return $this->inventoryModel->getUserInventoryIDsByMask($username, $permission_mask);
    }
    public function getUserInventoryTablesByMask($username, $permission_mask) {
        $ids = $this->getUserInventoryIDsByMask($username, $permission_mask);
        $inventories = array();
        foreach ($ids as $id) {
            $inventories[] = $this->inventoryModel->getInventoryTableById($id);
        }
        return $inventories;
    }
  }
?>