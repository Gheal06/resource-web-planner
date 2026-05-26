<?php
require_once __DIR__ . "/../models/InventoryModel.php";

class DashboardController {
    private $inventoryModel;

    public function __construct($connection) {
        $this->inventoryModel = new InventoryModel($connection);
    }

    public function getUserReadableInventoryTableIDs($username) {
        return $this->inventoryModel->getUserReadableInventoryTableIDs($username);
    }
    public function getUserReadableInventoryTables($username) {
        $ids = $this->inventoryModel->getUserReadableInventoryTableIDs($username);
        $inventories = array();
        foreach ($ids as $id) {
            $inventories[] = $this->inventoryModel->getInventoryTableById($id);
        }
        return $inventories;
    }
}
?>
