<div class="split-container3-l center-conent">
<h2 class="center-content">Resources</h2>
<table id="resource-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Quantity</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $inventory_id = $inventory['id'] ?? null;
        $rows = $inventory_id ? $inventoryController->getResourcesForInventory($currentUser, $inventory_id) : array();
        foreach ($rows as $row):
    ?>
    <tr>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['quantity']); ?> <?php echo htmlspecialchars($row['unit']); ?></td>
        <td><?php echo htmlspecialchars($row['description']); ?></td>
    </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="3"><a href="new_resource.php?inventory_id=<?php echo urlencode($inventory_id); ?>">Create new resource</a></td>
    </tr>
    </tbody>
</table>
</div>