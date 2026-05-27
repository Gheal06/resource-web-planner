<div>
<script>
    <?php require_once "js/toggle_table_contents.js"; ?>
</script>
<h2 class="center-content">Resources</h2>
<table id="resource-table">
    <thead onclick="toggleTableContents(event)">
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