<?php
    if (!isset($currentUser)) {
        $currentUser = null;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        <?php 
require_once "index.css"     // de modificat ca sa poata da cache
        ?>
    </style>
    <title>Resource Web Planner</title>
</head>
<body>
    <div id="page">
    <div id="nav">
        <div id="nav_left">
            <a href="index.php">Home</a>
        </div>
        <div id="nav_right">
            <?php if(!$currentUser): ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <?php else: ?>
            <span><?php echo htmlspecialchars($currentUser);?></span>
            <a href="logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>
    <div>