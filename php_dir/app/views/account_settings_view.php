<?php
    
?>
<div class="container full">
    <h2>Settings</h2>
    <form action= <?php echo $changePasswordAction; ?>  method="post">
        <label for="old-password" required>Old password:</label>
        <input type="password" name="old-password">
        <div class="sep"></div>
        <label for="new-password" required>New password:</label>
        <input type="password" name="new-password">
        <br>
        <label for="repeat-password" required>Repeat new password:</label>
        <input type="password" name="repeat-password">
        <br>
        <input type="submit" name = "change-password" value="Change Password">
    </form>
    <?php if (!empty($message ?? '')): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
</div>