<?php

class FonduriTransactionHistoryModel {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function addTransaction($fonduri_id, $fonduri_name, $currency_code, $inventory_id, $operation_type, $amount_change, $old_amount, $new_amount, $description = null, $created_by = null) {
        if ($fonduri_id === null || $amount_change === null || $operation_type === null) {
            return array('success' => false, 'message' => 'fonduri_id, amount_change and operation_type are required', 'code' => 'invalid_args');
        }

        $sql = "INSERT INTO fonduri_transaction_history (fonduri_id, fonduri_name, currency_code, inventory_id, operation_type, amount_change, old_amount, new_amount, description, created_by) 
                VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10) RETURNING id";
        $params = array($fonduri_id, $fonduri_name, $currency_code, $inventory_id, $operation_type, $amount_change, $old_amount, $new_amount, $description, $created_by);
        $res = @pg_query_params($this->connection, $sql, $params);
        if ($res === false) {
            $err = pg_last_error($this->connection);
            return array('success' => false, 'message' => $err, 'code' => 'db_error');
        }
        $row = pg_fetch_assoc($res);
        return isset($row['id']) ? array('success' => true, 'id' => $row['id']) : array('success' => false, 'message' => 'Failed to create transaction', 'code' => 'unknown_error');
    }

    public function getByFonduriId($fonduri_id, $limit = null) {
        $sql = "SELECT * FROM fonduri_transaction_history WHERE fonduri_id = $1 ORDER BY created_at DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . intval($limit);
        }
        $res = @pg_query_params($this->connection, $sql, array($fonduri_id));
        if (!$res) return array();
        $rows = array();
        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getByInventoryId($inventory_id, $limit = null) {
        $sql = "SELECT * FROM fonduri_transaction_history WHERE inventory_id = $1 ORDER BY created_at DESC";
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
        $res = @pg_query_params($this->connection, "SELECT * FROM fonduri_transaction_history WHERE id = $1", array($id));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function getStatistics($fonduri_id, $start_date, $end_date) {
        $sql = "SELECT * FROM fonduri_transaction_history
                WHERE fonduri_id = $1 AND created_at >= $2 AND created_at <= $3 ORDER BY created_at ASC";
        $res = @pg_query_params($this->connection, $sql, array($fonduri_id, $start_date, $end_date));
        if (!$res) return null;
        return pg_fetch_all($res);
    }
}

?>
