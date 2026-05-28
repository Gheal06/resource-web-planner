<?php
require_once "header.php";
require_once "app/controllers/ResourceController.php";
require_once "conn.php";
$createTagAction = 'new_tag_for_resource.php';
$resourceController = new ResourceController($connection);
$message = $resourceController -> addTag();
require_once "app/views/header_view.php";
require_once "app/views/new_tag_view.php";
require_once "footer.php";
?>