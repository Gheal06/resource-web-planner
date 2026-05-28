<?php

  require_once __DIR__ . "/../models/CurrencyModel.php";
  class CurrencyService {
    private $connection;
    private $currencyModel;
    public function __construct($connection) {
      $this->connection = $connection;
      $this->currencyModel = new CurrencyModel($connection);
    }

    public function getCurrencyById($id) {
        return $this->currencyModel->getCurrencyById($id);
    }
    public function getAllCurrencies() {
        return $this->currencyModel->getAllCurrencies();
    }
    public function getCurrencyByCode($code) {
        return $this->currencyModel->getCurrencyByCode($code);
    }
    public function getCurrencyExchangeRates($code) {
        $normalizedCode = strtoupper(trim((string)$code));
        if ($normalizedCode === '') {
            return null;
        }

        $currency = @file_get_contents($this->exchangeRateURL($normalizedCode));
        if ($currency === false || $currency === '') return null;
        $currencyData = json_decode($currency, true);
        if (!$currencyData || $currencyData['result'] !== 'success') return null;
        $exchangeRates = $currencyData['conversion_rates'];
        return $exchangeRates;
    }
    public function getCurrencyPairExchangeRate($from, $to) {
        $exchangeRates = $this->getCurrencyExchangeRates($from);
        if (!$exchangeRates || !isset($exchangeRates[$to])) return null;
        return $exchangeRates[$to];
    }
    public function exchangeRateURL($code) {
      $API_KEY = getenv("EXCHANGERATE_API_KEY");
      $url = "https://v6.exchangerate-api.com/v6/{$API_KEY}/latest/" . rawurlencode($code);
      return $url;
    }

  }