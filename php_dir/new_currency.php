<?php
require_once "header.php";
require_once "app/services/CurrencyService.php";
require_once "app/controllers/ResourceController.php";

$createCurrencyAction = 'new_currency.php';
$currencyService = new CurrencyService($connection);
$currencies = $currencyService->getAllCurrencies() ?? array();
$resourceController = new ResourceController($connection);
$message = $resourceController -> addFund($currentUser);
require_once "app/views/header_view.php";
require_once "app/views/new_currency_view.php";
require_once "footer.php";
?>