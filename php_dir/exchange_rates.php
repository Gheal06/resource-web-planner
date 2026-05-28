<?php
require_once "header.php";
require_once __DIR__ . "/app/services/CurrencyService.php";

$currencyService = new CurrencyService($connection);
$requestedCode = strtoupper(trim($_GET['currency_code'] ?? ''));
$exchangeRates = null;
$rateError = '';

if ($requestedCode !== '') {
    $exchangeRates = $currencyService->getCurrencyExchangeRates($requestedCode);
    if ($exchangeRates === null) {
        $rateError = 'Unable to load exchange rates for ' . $requestedCode . '.';
    } else {
        ksort($exchangeRates);
    }
}

require_once __DIR__ . "/app/views/header_view.php";
require_once __DIR__ . "/app/views/exchange_rates_view.php";
require_once __DIR__ . "/footer.php";
?>