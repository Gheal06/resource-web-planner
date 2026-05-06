<?php

require ("conn.php");
require ("initdb.php");
$message = "";

if (isset($_REQUEST["register"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if user exists
    $checkUserResult = pg_query_params($connection, "SELECT * FROM user_tables WHERE user_name = $1", array($username));
    
    if (pg_num_rows($checkUserResult) > 0) {
        $message = "Username already exists.";
    } else {
        $insertResult = pg_query_params($connection, "INSERT INTO user_tables (user_name, password_hash) VALUES ($1, $2)", array($username, $hashedPassword));
        
        if ($insertResult) {
            $message = "Registration successful!";
        } else {
            $message = "Error: " . pg_last_error($connection);
        }
    }
}

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
    <form action="" method="post">
        <label for="username">Username: </label>
        <input type="text" name="username" id="username">
        <label for="password">Password: </label>
        <input type="password" name="password" id="password">
        <input type="submit" name="register" value="Register">
    </form>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
</body>
</html>