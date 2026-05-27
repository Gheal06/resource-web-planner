<?php
require_once "header.php";
require_once "app/controllers/ResourceController.php";

$createTagAction = 'new_tag.php';
$resourceController = new ResourceController();
$message = $resourceController -> addTag();
require_once "app/views/header_view.php";
require_once "app/views/new_tag_view.php";
require_once "footer.php";
?>