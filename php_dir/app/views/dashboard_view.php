<?php require_once __DIR__ . '/../../header.php'; ?>
<div class="container full">
    <h2>Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($currentUser ?? ''); ?>!</p>
    <p>You have acces to <?php echo count($inventoryTableIDs); ?> inventories:</p>
    <p><a href="index.php?action=create-inventory">Create new inventory</a></p>
    <ul>
        <?php foreach ($inventoryTableIDs as $inv): ?>
            <li><?php echo $inv['name']; ?></li>
            <li><?php echo "**" . $inv['description'] . "**"; ?></li>
        <?php endforeach; ?>
    </ul>
    <!-- <p><a href="index.php?action=logout">Logout</a></p> -->
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
