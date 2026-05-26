<?php require_once __DIR__ . '/../../header.php'; ?>
<div class="container">
    <h2>Login</h2>
    <form action="index.php?action=login" method="post">
        <label for="username">Username: </label>
        <input type="text" name="username" id="username" required>
        <label for="password">Password: </label>
        <input type="password" name="password" id="password" required>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="login" value="Login">
        </div>
    </form>
    <?php if (!empty(
$message ?? '')): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <p>Don't have an account? <a href="index.php?action=register">Register</a></p>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
