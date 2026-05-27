<?php
require_once __DIR__ . "/../services/InventoryManagementService.php";

class InventoryController {
    private $inventoryManagementService;
    public function __construct($connection) {
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
}
?>
