<?php

class FonduriModel {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function getFonduriByInventoryId($inventory_id) {
        $res = pg_query_params($this->connection, "SELECT * FROM fonduri WHERE inventory_id = $1", array($inventory_id));
        if (!$res) return null;
        $fonduri = array();
        while ($row = pg_fetch_assoc($res)) {
            $fonduri[] = $row;
        }
        return $fonduri;
    }

    public function getFonduriByInventoryIdAndCurrency($inventory_id, $currency_code) {
        $res = pg_query_params($this->connection, "SELECT * FROM fonduri WHERE inventory_id = $1 AND currency_code = $2", array($inventory_id, $currency_code));
        if (!$res) return null;
        return pg_fetch_assoc($res);
    }

    public function addFonduri($inventory_id, $amount, $currency_code) {
        // Try to update existing row by increasing the amount
        $res = pg_query_params($this->connection,
            "UPDATE fonduri SET amount = amount + $1 WHERE inventory_id = $2 AND currency_code = $3",
            array($amount, $inventory_id, $currency_code)
        );

        if ($res && pg_affected_rows($res) > 0) {
            return $res;
        }

        // If no row was updated, insert a new one (amount is the initial value)
        return pg_query_params($this->connection,
            "INSERT INTO fonduri (amount, currency_code, inventory_id) VALUES ($1, $2, $3)",
            array($amount, $currency_code, $inventory_id)
        );
    }

    public function setFonduri($inventory_id, $amount, $currency_code) {
        $res = pg_query_params($this->connection, "SELECT id FROM fonduri WHERE inventory_id = $1 AND currency_code = $2", array($inventory_id, $currency_code));
        if (!$res) return null;
        $row = pg_fetch_assoc($res);
        if ($row) {
            return pg_query_params($this->connection, "UPDATE fonduri SET amount = $1 WHERE id = $2", array($amount, $row['id']));
        } else {
            return pg_query_params($this->connection, "INSERT INTO fonduri (amount, currency_code, inventory_id) VALUES ($1, $2, $3)", array($amount, $currency_code, $inventory_id));
        }
    }

}

?>

