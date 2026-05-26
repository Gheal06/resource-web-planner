<?php
  class InventoryModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getInventoryById($id) {
        $res = pg_query_params($this->conn, "SELECT * FROM inventories WHERE id = $1", array($id));
        return pg_fetch_assoc($res);
    }
    public function getUserInventoryIDsByMask($username, $permission_mask) {
      // returneaza id-urile tabelelor pt care utilizatorul are MACAR drepturile din permission_mask
        $res = pg_query_params($this->conn, "SELECT inventory_id FROM user_inventory_permission JOIN users ON user_inventory_permission.user_id = users.id WHERE users.username = $1 AND (user_inventory_permission.permissions & $2) > 0", array($username, $permission_mask));
        $ids = array();
        while ($row = pg_fetch_assoc($res)) {
            $ids[] = $row['inventory_id'];
        }

        return $ids;
    }
  }

?>