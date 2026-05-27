<?php

class TransactionModel {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function create($resource_id, $quantity_change, $currency_code = null, $total_price_change = null, $start_timestamp = null, $end_timestamp = null, $frequency = null, $description = null) {
        if ($resource_id === null || $quantity_change === null) {
            return array('success' => false, 'message' => 'resource_id and quantity_change are required', 'code' => 'invalid_args');
        }

        $sql = "INSERT INTO transactions (resource_id, currency_code, quantity_change, total_price_change, start_timestamp, end_timestamp, frequency, description) VALUES ($1, $2, $3, $4, COALESCE($5, NOW()), $6, $7, $8) RETURNING id";
        $params = array($resource_id, $currency_code, $quantity_change, $total_price_change, $start_timestamp, $end_timestamp, $frequency, $description);
        $res = @pg_query_params($this->connection, $sql, $params);
        if ($res === false) {
            $err = pg_last_error($this->connection);
            return array('success' => false, 'message' => $err, 'code' => 'db_error');
        }
        $row = pg_fetch_assoc($res);
        return isset($row['id']) ? $row['id'] : false;
    }

    public function getById($tx_id) {
        $res = @pg_query_params($this->connection, "SELECT * FROM transactions WHERE id = $1", array($tx_id));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function getByResourceId($resource_id) {
        $res = @pg_query_params($this->connection, "SELECT * FROM transactions WHERE resource_id = $1 ORDER BY start_timestamp", array($resource_id));
        if (!$res) return array();
        $rows = array();
        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getByInventoryId($inventory_id) {
        $sql = "SELECT t.* FROM transactions t JOIN resources r ON r.id = t.resource_id WHERE r.inventory_id = $1 ORDER BY t.start_timestamp";
        $res = @pg_query_params($this->connection, $sql, array($inventory_id));
        if (!$res) return array();
        $rows = array();
        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function checkApplicable($tx_id) {
        $res = @pg_query_params($this->connection, "SELECT check_transaction_applicable($1) AS applicable", array($tx_id));
        if ($res === false) {
            return array('success' => false, 'message' => pg_last_error($this->connection), 'code' => 'db_error');
        }
        $row = pg_fetch_assoc($res);
        return isset($row['applicable']) ? ($row['applicable'] === 't' || $row['applicable'] === true) : false;
    }

    public function apply($tx_id) {
        $res = @pg_query_params($this->connection, "SELECT apply_transaction($1)", array($tx_id));
        if ($res === false) {
            return array('success' => false, 'message' => pg_last_error($this->connection), 'code' => 'db_error');
        }
        return array('success' => true, 'message' => 'Transaction applied.');
    }

    public function skip($tx_id) {
        $res = @pg_query_params($this->connection, "SELECT skip_transaction($1)", array($tx_id));
        if ($res === false) {
            return array('success' => false, 'message' => pg_last_error($this->connection), 'code' => 'db_error');
        }
        return array('success' => true, 'message' => 'Transaction skipped (start_timestamp advanced or removed).');
    }

}

?>