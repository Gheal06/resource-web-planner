<?php
  class InventoryModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getInventoryTableById($id) {
        $res = pg_query_params($this->conn, "SELECT * FROM inventory_table WHERE id = $1", array($id));
        return pg_fetch_assoc($res);
    }
    public function getUserInventoryIDsByMask($username, $permission_mask) {
      // returneaza id-urile tabelelor pt care utilizatorul are MACAR drepturile din permission_mask
        $res = pg_query_params($this->conn, "SELECT inventory_id FROM user_table_permission JOIN user_table ON user_table_permission.user_id = user_table.id WHERE user_table.username = $1 AND (user_table_permission.permissions & $2) > 0", array($username, $permission_mask));
        $ids = array();
        while ($row = pg_fetch_assoc($res)) {
            $ids[] = $row['inventory_id'];
        }

        return $ids;
    }
  }

?>