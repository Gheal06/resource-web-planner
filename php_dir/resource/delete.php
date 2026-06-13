<?php
require_once "../conn.php";
require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/ResourceController.php";

verifyAccess($inventoryId, EDIT);

$controller = new AuthController($connection);
$username = $controller -> getCurrentUser();
$resourceController = new ResourceController($connection);
$resourceController -> removeResource($username);
?>