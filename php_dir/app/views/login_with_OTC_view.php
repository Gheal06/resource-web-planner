
<div class="container">
    <h2>Login</h2>
    <form action= <?php echo $OTCLoginAction; ?>  method="post">
        <label for="username">Username: </label>
        <input type="text" name="username" id="username" required>
        <label for="OTC">OTC: </label>
        <input type="password" name="OTC" id="OTC" required>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="OTC_login" value="Login">
        </div>
    </form>
    <?php if (!empty(
$message ?? '')): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
