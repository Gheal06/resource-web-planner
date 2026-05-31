
<div class="container full">
    <a href="index.php">Inventory List</a>
    <div class="sep"></div>
    <h2><?php echo htmlspecialchars($inventory['name'] ?? 'Inventory'); ?> <span style="font-size: 0.8em; color: #666;">(Owner: <?php echo htmlspecialchars($inventoryOwnerUsername ?? 'Unknown'); ?>)</span></h2>
    <p><?php echo htmlspecialchars($inventory['description'] ?? ''); ?></p>
    <div class="sep"></div>

    <?php

        if ($currentUser == $inventoryOwnerUsername):
    ?>
    <div style="margin: 10px 0;">
        <a href="manage_access.php?inventory_id=<?php echo urlencode($inventory['id']); ?>" style="padding: 8px 12px; background-color: #5555FF; color: white; text-decoration: none; border-radius: 4px;">Manage Access</a>
    </div>
    <?php endif; ?>

    <?php require_once __DIR__ . '/tags_view.php'; ?>
    <?php require_once __DIR__ . '/fonduri_view.php'; ?>

    <?php require_once __DIR__ . '/resources_view.php'; ?>
    <?php require_once __DIR__ . '/transaction_history_view.php'; ?>
</div>
    <!-- <p><a href="index.php?action=logout">Logout</a></p> -->
<?php require_once __DIR__ . '/../../footer.php'; ?>