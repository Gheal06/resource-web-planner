
<div class="container full">
    <a href="index.php">Inventory List</a>
    <div class="sep"></div>
    <h2><?php echo htmlspecialchars($inventory['name'] ?? 'Inventory'); ?></h2>
    <p><?php echo htmlspecialchars($inventory['description'] ?? ''); ?></p>
    <div class="sep"></div>
    <?php require_once __DIR__ . '/tags_view.php'; ?>
    <?php require_once __DIR__ . '/fonduri_view.php'; ?>

    <?php require_once __DIR__ . '/resources_view.php'; ?>
    <?php require_once __DIR__ . '/transaction_history_view.php'; ?>
</div>
    <!-- <p><a href="index.php?action=logout">Logout</a></p> -->
<?php require_once __DIR__ . '/../../footer.php'; ?>