<?php

  require_once __DIR__ . "/../controllers/CurrencyController.php";
  class CurrencyService {
    private $connection;
    private $currencyController;
    public function __construct($connection) {
      $this->connection = $connection;
      $this->currencyController = new CurrencyController($connection);
    }

    public function getCurrencyById($id) {
        return $this->currencyController->getCurrencyById($id);
    }
    public function getAllCurrencies() {
        return $this->currencyController->getAllCurrencies();
    }
    public function getCurrencyByCode($code) {
        return $this->currencyController->getCurrencyByCode($code);
    }
    public function getCurrencyExchangeRates($code) {
        $currency = $this->getCurrencyByCode($code);
        if (!$currency) {
            return null;
        }
        $exchangeRates = json_decode($currency, true);
        return $exchangeRates;
    }
    public function exchangeRateURL($code) {
      $API_KEY = getenv("EXCHANGERATE_API_KEY");
      $url = "https://v6.exchangerate-api.com/v6/$API_KEY/latest/$code";
      return $url;
    }

  }