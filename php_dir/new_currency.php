<?php
require_once "header.php";
require_once "app/controllers/ResourceController.php";

$createCurrencyAction = 'new_currency.php';
$resourceController = new ResourceController($connection);
$message = $resourceController -> addCurrency(); 
require_once "app/views/header_view.php";
require_once "app/views/new_currency_view.php";
require_once "footer.php";
?>