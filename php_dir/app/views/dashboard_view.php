
<div class="container full">
    <h2>Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($currentUser ?? ''); ?>!</p>
    <p>You have acces to <?php echo count($inventoryTableIDs); ?> inventories:</p>
    <table id="inventory-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($inventoryTableIDs as $inv): ?>
            <tr>
                <td><a href="inventory.php?inventory_id=<?php echo urlencode($inv['id']); ?>"><?php echo htmlspecialchars($inv['name']); ?></a></td>
                <td><?php echo htmlspecialchars($inv['description'] ?? ''); ?></td>
                <td>
                    <form onsubmit="return confirm('Are you sure you want to remove this inventory?');" action="inventory/delete.php?inventory_id=<?php echo urlencode($inv['id']);?>" method="post">
                        <input name="submitRemoveInventory" type="submit" value="Delete">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><a href="new_inventory.php">Create new inventory</a></td>
        </tr>
        </tbody>
    </table>
    <!-- <p><a href="index.php?action=logout">Logout</a></p> -->
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
