<?php

class CurrencyModel {
  private $conn;
  public function __construct($connection) {
      $this->conn = $connection;
  }

  public function getAllCurrencies() {
      $res = pg_query($this->conn, "SELECT * FROM currencies");
      if (!$res) return null;
      $currencies = array();
      while ($row = pg_fetch_assoc($res)) {
          $currencies[] = $row;
      }
      return $currencies;
  }
  public function getCurrencyById($id) {
      $res = pg_query_params($this->conn, "SELECT * FROM currencies WHERE id = $1", array($id));
      if (!$res) return null;
      return pg_fetch_assoc($res);
  }
  public function getCurrencyByCode($code) {
      $res = pg_query_params($this->conn, "SELECT * FROM currencies WHERE code = $1", array($code));
      if (!$res) return null;
      return pg_fetch_assoc($res);
  }
}



?>