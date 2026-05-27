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

    public function create($name, $description) {
        return pg_query_params($this->conn, "INSERT INTO inventories (name, description) VALUES ($1, $2) RETURNING id", array($name, $description));
        
    }
  }

?>