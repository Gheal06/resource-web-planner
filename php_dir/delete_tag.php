<?php
require_once "header.php";
require_once "app/controllers/ResourceController.php";

$resourceController = new ResourceController($connection);
$resourceController->removeTag($currentUser);
?>