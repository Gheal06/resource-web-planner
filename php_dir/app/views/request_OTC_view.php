
<div class="container">
    <h2>Login</h2>
    <form action= <?php echo $OTCrequestAction; ?>  method="post">
        <label for="email">Email: </label>
        <input type="email" name="email" id="email" required>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="OTC_request" value="Request OTC">
        </div>
    </form>
    <?php if (!empty(
$message ?? '')): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
