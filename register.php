<?php

require("conn.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css" type="text/css">
    <title>Resource Web Planner</title>
</head>
<body>
    <div id="page">
    <div id="nav">
        <span>Login</span>
        <span>Register</span>
    </div>
    </div>
    <form action="post">
        <input type="text" name="username" id="username">
        <label for="username">Username: </label>
        <input type="text" name="password" id="password">
        <label for="username">Password: </label>
        <input type="submit" name="submit">
    </form>
</body>
</html>