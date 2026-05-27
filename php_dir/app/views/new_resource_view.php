<?php
    $inventoryId = $_GET['inventory_id'];
    $action = is_null($inventoryId) ? "new_resource.php" : ($createResourceAction . "?inventory_id=". $inventoryId);
?>
<div class="container new-inventory">
    <h2 class="center-content">Create new resource</h2>
    <form action="<?php echo "$action"?>"  method="post">
        <label for="resource-name">Resource Name: </label>
        <input type="text" name="resource-name" id="resource-name" required> <br>
        <label for="description">Description: </label>
        <textarea name="description" id="description" maxlength="255" rows="10"> </textarea>
        <?php if (!empty( $message ?? '')): ?>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="submit" value="Submit">
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
