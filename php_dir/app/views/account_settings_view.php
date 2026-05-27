<?php
    
?>
<div class="container full">
    <h2>Settings</h2>
    <form action="update-settings" method="post">
        <label for="old-password" required> Old password:</label>
        <input type="password" name="old-password">
        <br>
        <label for="email" maxlength="64" required> Email:</label>
        <input type="text" name="email">
        <br>
        <label for="new-password" required>New password:</label>
        <input type="password" name="new-password">
        <br>
        <label for="repeat-password" required>Repeat new password:</label>
        <input type="password" name="repeat-password">
        <br>
        <input type="submit">
    </form>
</div>