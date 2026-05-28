<?php
require_once "../conn.php";
require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/ResourceController.php";

$controller = new AuthController($connection);
$user = $controller -> getCurrentUser();
if(!$user || !isset($user['username'])){
    header("Location: error.php");
    exit();
}
$username = $user['username'];
$resourceController = new ResourceController($connection);
$resourceController -> removeResource($username);
?>