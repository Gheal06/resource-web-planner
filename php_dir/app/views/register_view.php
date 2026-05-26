
<div class="container">
    <h2>Register</h2>
    <form action="index.php?action=register" method="post">
        <label for="username">Username: </label>
        <input type="text" name="username" id="username" required>
        <label for="email">Email: </label>
        <input type="email" name="email" id="email" required>
        <label for="password">Password: </label>
        <input type="password" name="password" id="password" required>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="register" value="Register">
        </div>
    </form>
    <?php if (!empty(
$message ?? '')): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <p>Already have an account? <a href="index.php?action=login">Login</a></p>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
