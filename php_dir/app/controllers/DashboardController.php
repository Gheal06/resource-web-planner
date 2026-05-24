<?php
require_once __DIR__ . "/../models/InventoryModel.php";

class DashboardController {
    private $inventoryModel;

    public function __construct($connection) {
        $this->inventoryModel = new InventoryModel($connection);
    }

    public function getUserInventoryTableIDs($username) {
        return $this->inventoryModel->getUserInventoryTableIDs($username);
    }
}
?>
