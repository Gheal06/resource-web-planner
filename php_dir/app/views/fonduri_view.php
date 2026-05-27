<div>
<h2 class="center-content">Funds</h2>
<table id="currency-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Currency</th>
            <th>Quantity</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $inventory_id = $inventory['id'] ?? null;
        $rows = $inventory_id ? $inventoryController->getFonduriForInventory($currentUser, $inventory_id) : array();
        foreach ($rows as $row):
    ?>
    <tr>
        <td><?php echo htmlspecialchars($row['name'] ?? '-'); ?></td>
        <td><?php echo htmlspecialchars($row['currency_code']); ?></td>
        <td><?php echo htmlspecialchars($row['amount']); ?></td>
        <td><?php echo htmlspecialchars($row['description'] ?? '-'); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="4"><a href="new_currency.php?inventory_id=<?php echo urlencode($inventory_id); ?>">Add new currency</a></td>
    </tr>
    </tbody>
</table>
</div>