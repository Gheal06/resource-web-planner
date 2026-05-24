<?php
  class InventoryModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getInventoryTableById($id) {
        $res = pg_query_params($this->conn, "SELECT * FROM inventory_table WHERE id = $1", array($id));
        $inv = pg_fetch_assoc($res);
        $items = array();
        return array(
          'id' => $inv['id'],
          'name' => $inv['name'],
          'description' => $inv['description'],
          'items' => $items,
        );
    }
    public function getUserInventoryTableIDs($username) {
        $res = pg_query_params($this->conn, "SELECT inventory_id FROM user_table_permission JOIN user_table ON user_table_permission.user_id = user_table.id WHERE user_table.username = $1 AND user_table_permission.can_read = true", array($username));
        $ids = array();
        while ($row = pg_fetch_assoc($res)) {
            $ids[] = $row['inventory_id'];
        }

        return $ids;
    }

  }

?>