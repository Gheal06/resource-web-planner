<?php
    require_once "app/services/GravatarService.php";
    $gravatarService = new GravatarService($connection);
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
        <div id="nav-left">
            <a href="index.php">Home</a>
        </div>
        <div id="nav-right">
            <?php if(!$currentUser): ?>
              <a href="login.php" class="centerY">Login</a>
              <a href="register.php" class="centerY">Register</a>
            <?php else: ?>
              <img class="gravatar-image centerY" src="<?php echo $gravatarService->getGravatarUrl($currentUser); ?>" alt="">
              <a href="account.php" class="centerY"><?php echo htmlspecialchars($currentUser);?></a>
            <a href="logout.php" class="centerY">Logout</a>
            <?php endif; ?>
        </div>
    </div>
    <div>