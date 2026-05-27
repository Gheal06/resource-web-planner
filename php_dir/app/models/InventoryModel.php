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

    public function create($name, $description, $owner_user_id) {
        $res = pg_query_params($this->conn, "INSERT INTO inventories (name, description, owner_user_id) VALUES ($1, $2, $3) RETURNING id", array($name, $description, $owner_user_id));
        if (!$res) return false;
        $row = pg_fetch_assoc($res);
        return $row ? $row['id'] : false;
    }

    public function beginTransaction() {
        return pg_query($this->conn, 'BEGIN');
    }

    public function commitTransaction() {
        return pg_query($this->conn, 'COMMIT');
    }

    public function rollbackTransaction() {
        return pg_query($this->conn, 'ROLLBACK');
    }

    public function update($id, $name, $description) {
        return pg_query_params($this->conn, "UPDATE inventories SET name = $1, description = $2 WHERE id = $3", array($name, $description, $id));
    }
    public function updateName($id, $name) {
        return pg_query_params($this->conn, "UPDATE inventories SET name = $1 WHERE id = $2", array($name, $id));
    }
    public function updateDescription($id, $description) {
        return pg_query_params($this->conn, "UPDATE inventories SET description = $1 WHERE id = $2", array($description, $id));
    }

    
    public function delete($id) {
        return pg_query_params($this->conn, "DELETE FROM inventories WHERE id = $1", array($id));
    }

  }

?>