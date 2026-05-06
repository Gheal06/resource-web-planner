<?php require_once "header.php" ?>
<?php

require_once ("conn.php");
require_once ("initdb.php");
$message = "";

if (isset($_REQUEST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if user exists
    $checkUserResult = pg_query_params($connection, "SELECT * FROM user_tables WHERE user_name = $1", array($username));
    
    if (pg_num_rows($checkUserResult) == 0) {
        $message = "Invalid username or password.";
    } else {
        $user = pg_fetch_assoc($checkUserResult);
        if (password_verify($password, $user["password_hash"])) {
            $message = "Login successful!";
            $_SESSION["username"] = $username;
        } else {
            $message = "Invalid username or password.";
        }
    }
}

?>

<form action="" method="post">
    <label for="username">Username: </label>
    <input type="text" name="username" id="username">
    <label for="password">Password: </label>
    <input type="password" name="password" id="password">
    <input type="submit" name="login" value="Login">
</form>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>
<?php require_once "footer.php" ?>