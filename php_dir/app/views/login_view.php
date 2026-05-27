
<div class="container">
    <h2>Login</h2>
    <form action= <?php echo $loginAction; ?>  method="post">
        <label for="username">Username: </label>
        <input type="text" name="username" id="username" required>
        <label for="password">Password: </label>
        <input type="password" name="password" id="password" required>
        <?php if (!empty( $message ?? '')): ?>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="login" value="Login">
        </div>
    </form>

    <p style="text-align:center; margin-top:10px;"><a href=<?php echo $OTCrequestAction; ?>>Forgot password?</a></p>

    <p>Don't have an account? <a href="index.php?action=register">Register</a></p>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
