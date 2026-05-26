<?php
require_once __DIR__ . "/../controllers/InventoryController.php";
  class InventoryManagementService {
    private $inventoryController;

    public function __construct($connection) {
      $this->inventoryController = new InventoryController($connection);
    }
    public function getUserReadableInventoryTableIDs($username) {
        return $this->inventoryController->getUserInventoryIDsByMask($username, 1); // 1 = bitmask pentru dreptul de read
    }
    public function getUserReadableInventoryTables($username) {
        return $this->inventoryController->getUserInventoryTablesByMask($username, 1); // 1 = bitmask pentru dreptul de read
    }

    
    public function getUserInventoryIDsByMask($username, $permission_mask) {
        return $this->inventoryController->getUserInventoryIDsByMask($username, $permission_mask);
    }
    public function getUserInventoryTablesByMask($username, $permission_mask) {
        return $this->inventoryController->getUserInventoryTablesByMask($username, $permission_mask);
    }
  }
?>