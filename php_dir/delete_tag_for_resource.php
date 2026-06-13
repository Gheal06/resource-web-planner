<?php
require_once "header.php";
require_once "app/controllers/ResourceController.php";

verifyAccess($inventoryId, EDIT);

$resourceController = new ResourceController($connection);
$resourceController->removeTagFromResource($currentUser);
?>