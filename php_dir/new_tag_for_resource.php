<?php
require_once "header.php";
require_once "app/controllers/ResourceController.php";
require_once "conn.php";
$resourceController = new ResourceController($connection);
$resourceController -> addTagToResource($currentUser);
exit();
?>