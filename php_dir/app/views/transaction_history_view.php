<?php
require_once __DIR__ . "/../models/ResourceTransactionHistoryModel.php";
require_once __DIR__ . "/../models/FonduriTransactionHistoryModel.php";

$inventory_id = $inventory['id'] ?? null;
if (!$inventory_id) {
    echo "<div><p>Inventory ID not found.</p></div>";
    return;
}

$resourceHistoryModel = new ResourceTransactionHistoryModel($connection);
$currencyHistoryModel = new FonduriTransactionHistoryModel($connection);

$resourceHistory = $resourceHistoryModel->getByInventoryId($inventory_id, 100);
$currencyHistory = $currencyHistoryModel->getByInventoryId($inventory_id, 100);

// Merge and sort by timestamp descending
$allHistory = array();
if (is_array($resourceHistory)) {
    foreach ($resourceHistory as $record) {
        $record['type'] = 'resource';
        $allHistory[] = $record;
    }
}
if (is_array($currencyHistory)) {
    foreach ($currencyHistory as $record) {
        $record['type'] = 'currency';
        $allHistory[] = $record;
    }
}

// Sort by created_at descending
usort($allHistory, function($a, $b) {
    return strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? '');
});
?>
<div>
    <script>
        <?php require_once "js/toggle_table_contents.js"; ?>
    </script>
    <h2 class="center-content">Transaction History</h2>
    <table id="history-table">
        <thead onclick="toggleTableContents(event)">
            <tr>
                <th>Item</th>
                <th>Operation</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (count($allHistory) > 0):
            foreach ($allHistory as $record):
                $itemName = htmlspecialchars($record['resource_name'] ?? $record['fonduri_name'] ?? $record['currency_code'] ?? 'Unknown');
                $operation = htmlspecialchars($record['operation_type'] ?? '');
                $amount = htmlspecialchars($record['quantity_change'] ?? $record['amount_change'] ?? '');
                $description = htmlspecialchars($record['description'] ?? '');
                $timestamp = date('M d Y, H:i', strtotime($record['created_at'] ?? ''));
        ?>
        <tr>
            <td><?php echo $itemName; ?></td>
            <td><?php echo ucfirst($operation); ?></td>
            <td><?php echo $amount; ?></td>
            <td><?php echo $description; ?></td>
            <td><?php echo $timestamp; ?></td>
        </tr>
        <?php
            endforeach;
        else:
        ?>
        <tr>
            <td colspan="5" class="center-content">No transaction history</td>
        </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>