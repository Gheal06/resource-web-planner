<?php
require_once "header.php";
require_once "app/controllers/ResourceController.php";

verifyAccess($inventoryId, EDIT);

$createResourceAction = 'new_resource.php';
$resourceController = new ResourceController($connection);
$message = $resourceController -> addResource($currentUser); 
require_once "app/views/header_view.php";
require_once "app/views/new_resource_view.php";
require_once "footer.php";
?>