<div>
<script>
    <?php require_once "js/toggle_table_contents.js"; ?>
</script>
<h2 class="center-content">Funds</h2>
<table id="currency-table">
    <thead onclick="toggleTableContents(event)">
        <tr>
            <th>Name</th>
            <th>Currency</th>
            <th>Quantity</th>
            <th>Description</th>
            <th>Actions</th>
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
        <td>
            <div class="centerY flex flex-gap center-content">
                <a href="transaction.php?inventory_id=<?php echo urlencode($inventory_id);?>&fonduri_id=<?php echo urlencode($row['id']);?>&operation=add" style="padding: 4px 8px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 3px; font-size: 0.85em;">+</a>
                <a href="transaction.php?inventory_id=<?php echo urlencode($inventory_id);?>&fonduri_id=<?php echo urlencode($row['id']);?>&operation=subtract" style="padding: 4px 8px; background-color: #ff9800; color: white; text-decoration: none; border-radius: 3px; font-size: 0.85em;">−</a>
                <form onsubmit="return confirm('Are you sure you want to remove this fund?');" action="delete_currency.php?inventory_id=<?php echo urlencode($inventory_id); ?>&currencyId=<?php echo urlencode($row['id']); ?>" method="post">
                    <input name="submitRemoveCurrency" type="submit" value="Delete" style="padding: 4px 8px; background-color: #f44336; color: white; border: none; border-radius: 3px; font-size: 0.85em; cursor: pointer;">
                </form>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="5"><a href="new_currency.php?inventory_id=<?php echo urlencode($inventory_id); ?>">Add new currency</a></td>
    </tr>
    </tbody>
</table>
</div>