<?php

class FonduriModel {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function getFonduriByInventoryId($inventory_id) {
        $res = @pg_query_params($this->connection, "SELECT * FROM fonduri WHERE inventory_id = $1", array($inventory_id));
        if (!$res) return null;
        $fonduri = array();
        while ($row = @pg_fetch_assoc($res)) {
            $fonduri[] = $row;
        }
        return $fonduri;
    }

    public function getFonduriByInventoryIdAndCurrency($inventory_id, $currency_code) {
        $res = @pg_query_params($this->connection, "SELECT * FROM fonduri WHERE inventory_id = $1 AND currency_code = $2", array($inventory_id, $currency_code));
        if (!$res) return null;
        return @pg_fetch_assoc($res);
    }

    public function getById($fonduri_id) {
        $res = @pg_query_params($this->connection, "SELECT * FROM fonduri WHERE id = $1", array($fonduri_id));
        if (!$res) return null;
        return @pg_fetch_assoc($res);
    }

    public function create($inventory_id, $currency_code, $threshold_amount, $name = null, $description = null) {
        
        return pg_query_params($this->connection,
            "INSERT INTO fonduri (amount, threshold_amount, currency_code, inventory_id, name, description) VALUES ($1, $2, $3, $4, $5, $6) RETURNING id",
            array(0, $threshold_amount  , $currency_code, $inventory_id, $name, $description)
        );
    }

    public function setFonduri($inventory_id, $amount, $currency_code, $name = null, $description = null) {
        $res = @pg_query_params($this->connection, "SELECT id FROM fonduri WHERE inventory_id = $1 AND currency_code = $2", array($inventory_id, $currency_code));
        if (!$res) return null;
        $row = @pg_fetch_assoc($res);
        if ($row) {
            return @pg_query_params($this->connection, "UPDATE fonduri SET amount = $1, name = $2, description = $3 WHERE id = $4", array($amount, $name, $description, $row['id']));
        } else {
            return @pg_query_params($this->connection, "INSERT INTO fonduri (amount, currency_code, inventory_id, name, description) VALUES ($1, $2, $3, $4, $5)", array($amount, $currency_code, $inventory_id, $name, $description));
        }
    }
    public function deleteFonduri($inventory_id, $currency_id) {
        return pg_query_params($this->connection, "DELETE FROM fonduri WHERE inventory_id = $1 AND id = $2", array($inventory_id, $currency_id));
    }

}
?>