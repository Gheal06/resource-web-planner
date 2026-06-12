<?php
    $tab = $_GET['tab'] ?? 'all';
    $url = 'inventory.php?inventory_id='.$inventory['id'];
    function makeTabUrl($tabName){
        global $url;
        return $url."&tab=".$tabName;
    }
?>
<div class="container full">
    <a href="index.php">Inventory List</a>
    <div class="sep"></div>
    <h2><?php echo htmlspecialchars($inventory['name'] ?? 'Inventory'); ?> <span style="font-size: 0.8em; color: #666;">(Owner: <?php echo htmlspecialchars($inventoryOwnerUsername ?? 'Unknown'); ?>)</span></h2>
    <p><?php echo htmlspecialchars($inventory['description'] ?? ''); ?></p>
    <div class="sep"></div>
    <div class="flex flex-wrap flex-gap10">
        <span>Tabs:</span>
        <a href="<?php echo makeTabUrl('all')?>">All</a>
        <a href="<?php echo makeTabUrl('general')?>">General</a>
        <a href="<?php echo makeTabUrl('tags')?>">Tags</a>
        <a href="<?php echo makeTabUrl('funds')?>">Funds</a>
        <a href="<?php echo makeTabUrl('resources')?>">Resources</a>
        <a href="<?php echo makeTabUrl('stats')?>">Stats</a>
        <a href="<?php echo makeTabUrl('transactions')?>">Transactions</a>
    </div>
    <div class="sep"></div>
    <?php if($tab == 'all' || $tab == 'general'): ?>
        <div class="inventory-actions">
            <form action="inventory/export.php?inventory_id=<?php echo urlencode($inventory['id'] ?? ''); ?>" method="post">
                <input type="hidden" name="type" value="csv">
                <input type="submit" value="Export CSV">
            </form>
            <form action="inventory/export.php?inventory_id=<?php echo urlencode($inventory['id'] ?? ''); ?>" method="post">
                <input type="hidden" name="type" value="json">
                <input type="submit" value="Export JSON">
            </form>
            <form action="inventory/export.php?inventory_id=<?php echo urlencode($inventory['id'] ?? ''); ?>" method="post">
                <input type="hidden" name="type" value="xml">
                <input type="submit" value="Export XML">
            </form>
        </div>
        <div class="sep"></div>

        <?php if ($currentUser == $inventoryOwnerUsername): ?>
            <div style="margin: 10px 0;">
                <a href="manage_access.php?inventory_id=<?php echo urlencode($inventory['id']); ?>" style="padding: 8px 12px; background-color: #5555FF; color: white; text-decoration: none; border-radius: 4px;">Manage Access</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if($tab == 'all' || $tab == 'tags'): ?>
        <?php require_once __DIR__ . '/tags_view.php'; ?>
    <?php endif; ?>
    <?php if($tab == 'all' || $tab == 'funds'): ?>
        <?php require_once __DIR__ . '/fonduri_view.php'; ?>
    <?php endif; ?>
    <?php if($tab == 'all' || $tab == 'resources'): ?>
    <?php require_once __DIR__ . '/resources_view.php'; ?>
    <?php endif; ?>
    <?php if($tab == 'all' || $tab == 'stats'): ?>
    <?php require_once __DIR__ . '/stats_view.php'; ?>
    <?php endif; ?>
    <?php if($tab == 'all' || $tab == 'transactions'): ?>
    <?php require_once __DIR__ . '/transaction_history_view.php'; ?>
    <?php endif; ?>
</div>
    <!-- <p><a href="index.php?action=logout">Logout</a></p> -->
<?php require_once __DIR__ . '/../../footer.php'; ?>