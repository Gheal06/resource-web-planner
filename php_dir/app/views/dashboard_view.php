
<div class="container full">
    <h2>Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($currentUser ?? ''); ?>!</p>
    <p>You have acces to <?php echo count($inventoryTableIDs); ?> inventories:</p>
    <table id="inventory-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($inventoryTableIDs as $inv): ?>
            <tr>
                <td><a href="inventory.php?inventory_id=<?php echo urlencode($inv['id']); ?>"><?php echo htmlspecialchars($inv['name']); ?></a></td>
                <td><?php echo htmlspecialchars($inv['description'] ?? ''); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><a href="index.php?action=create-inventory">Create new inventory</a></td>
            <td></td>
        </tr>
        </tbody>
    </table>
    <!-- <p><a href="index.php?action=logout">Logout</a></p> -->
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
