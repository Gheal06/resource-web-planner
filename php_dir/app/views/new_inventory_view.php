
<div class="container new-inventory">
    <h2 class="center-content">Create new inventory</h2>
    <form action="<?php echo $createInventoryAction; ?>"  method="post">
        <label for="inventory-name">Inventory Name: </label>
        <input type="text" name="inventory-name" required> <br>
        <label for="description">Description: </label>
        <textarea name="description" maxlength="255" rows="10"> </textarea>
        <?php if (!empty( $message ?? '')): ?>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="submitNewInventory" value="Submit">
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
