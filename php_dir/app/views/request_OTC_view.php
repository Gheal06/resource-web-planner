
<div class="container">
    <h2>Login</h2>
    <form action="index.php?action=login" method="post">
        <label for="email">Email: </label>
        <input type="email" name="email" id="email" required>
        <label for="OTC">OTC: </label>
        <input type="password" name="OTC" id="OTC" required>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="login" value="Login">
        </div>
    </form>
    <?php if (!empty(
$message ?? '')): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
