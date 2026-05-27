
<div class="container">
    <h2>Register</h2>
    <form action= <?php echo $registerAction; ?>  method="post">
        <label for="username">Username: </label>
        <input type="text" name="username" id="username" required>
        <label for="email">Email: </label>
        <input type="email" name="email" id="email" required>
        <label for="password">Password: </label>
        <input type="password" name="password" id="password" required>
        <label for="repeat-password">Repeat Password: </label>
        <input type="password" name="repeat-password" id="password" required>
        <?php if (!empty( $message ?? '')): ?>
            <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="register" value="Register">
        </div>
    </form>
    <p>Already have an account? <a href="index.php?action=login">Login</a></p>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
