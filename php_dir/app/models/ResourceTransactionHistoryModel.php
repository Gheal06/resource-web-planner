<?php

class ResourceTransactionHistoryModel {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function addTransaction($resource_id, $resource_name, $inventory_id, $operation_type, $quantity_change, $old_quantity, $new_quantity, $description = null, $created_by = null) {
        if ($resource_id === null || $quantity_change === null || $operation_type === null) {
            return array('success' => false, 'message' => 'resource_id, quantity_change and operation_type are required', 'code' => 'invalid_args');
        }

        $sql = "INSERT INTO resource_transaction_history (resource_id, resource_name, inventory_id, operation_type, quantity_change, old_quantity, new_quantity, description, created_by) 
                VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9) RETURNING id";
        $params = array($resource_id, $resource_name, $inventory_id, $operation_type, $quantity_change, $old_quantity, $new_quantity, $description, $created_by);
        $res = @pg_query_params($this->connection, $sql, $params);
        if ($res === false) {
            $err = pg_last_error($this->connection);
            return array('success' => false, 'message' => $err, 'code' => 'db_error');
        }
        $row = pg_fetch_assoc($res);
        return isset($row['id']) ? array('success' => true, 'id' => $row['id']) : array('success' => false, 'message' => 'Failed to create transaction', 'code' => 'unknown_error');
    }

    public function getByResourceId($resource_id, $limit = null) {
        $sql = "SELECT * FROM resource_transaction_history WHERE resource_id = $1 ORDER BY created_at DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . intval($limit);
        }
        $res = @pg_query_params($this->connection, $sql, array($resource_id));
        if (!$res) return array();
        $rows = array();
        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getByInventoryId($inventory_id, $limit = null) {
        $sql = "SELECT * FROM resource_transaction_history WHERE inventory_id = $1 ORDER BY created_at DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . intval($limit);
        }
        $res = @pg_query_params($this->connection, $sql, array($inventory_id));
        if (!$res) return array();
        $rows = array();
        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getById($id) {
        $res = @pg_query_params($this->connection, "SELECT * FROM resource_transaction_history WHERE id = $1", array($id));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function getStatistics($resource_id, $start_date, $end_date) {
        $sql = "SELECT * FROM resource_transaction_history
                WHERE resource_id = $1 AND created_at >= $2 AND created_at <= $3";
        $res = @pg_query_params($this->connection, $sql, array($resource_id, $start_date, $end_date));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }
}

?>
