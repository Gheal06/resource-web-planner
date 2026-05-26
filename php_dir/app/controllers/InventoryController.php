<?php
require_once __DIR__ . "/../services/InventoryManagementService.php";

class InventoryController {
    private $inventoryManagementService;
    public function __construct($connection) {
        $this->inventoryManagementService = new InventoryManagementService($connection);
    }
    
    public function getUserReadableInventoryTableIDs($username) {
        return $this->inventoryManagementService->getUserInventoryTableIDsByMask($username, 1);
    }
    public function getUserReadableInventoryTables($username) {
        return $this->inventoryManagementService->getUserInventoryTablesByMask($username, 1);
    }

    
    public function getUserInventoryIDsByMask($username, $permission_mask) {
        return $this->inventoryManagementService->getUserInventoryIDsByMask($username, $permission_mask);
    }
    public function getUserInventoryTablesByMask($username, $permission_mask) {
        return $this->inventoryManagementService->getUserInventoryTablesByMask($username, $permission_mask);
    }
}
?>
