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
        return $items;
    }

  }

>