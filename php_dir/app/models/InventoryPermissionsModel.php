<?php
  class InventoryPermissionsModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getUserInventoryIDsByMask($username, $required_permissions_mask) {
      // returneaza id-urile tabelelor pt care utilizatorul are MACAR drepturile din permission_mask
        $res = pg_query_params($this->conn, "SELECT inventory_id FROM user_inventory_permission JOIN users ON user_inventory_permission.user_id = users.id WHERE users.username = $1 AND (user_inventory_permission.permissions & $2) = $2", array($username, $required_permissions_mask));
      if (!$res) {
        return array();
      }
        $ids = array();
        while ($row = pg_fetch_assoc($res)) {
            $ids[] = $row['inventory_id'];
        }

        return $ids;
    }

    public function canUserAccessInventory($username, $inventory_id, $required_permissions_mask) {
      $res = pg_query_params($this->conn, "SELECT EXISTS(SELECT 1 FROM user_inventory_permission JOIN users ON user_inventory_permission.user_id = users.id WHERE users.username = $1 AND user_inventory_permission.inventory_id = $2 AND (user_inventory_permission.permissions & $3) = $3) AS has_access", array($username, $inventory_id, $required_permissions_mask));
      if (!$res) {
        return false;
      }

      $row = pg_fetch_assoc($res);
      return $row && $row['has_access'] == 't';
    }
  }

?>