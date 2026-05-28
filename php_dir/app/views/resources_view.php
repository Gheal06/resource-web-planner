
<div>
<script>
    <?php require_once "js/toggle_table_contents.js"; ?>
</script>
<h2 class="center-content">Resources</h2>
<table id="resource-table">
    <thead onclick="toggleTableContents(event)">
        <tr>
            <th style="width: 20%;">Tags</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $inventory_id = $inventory['id'] ?? null;
        $rows = $inventory_id ? $inventoryController->getResourcesForInventory($currentUser, $inventory_id) : array();
        foreach ($rows as $row):
    ?>
    <tr>
        <td>
            <?php
                $tags = $inventoryService -> getTagsForResource($currentUser, $row['id']);
                require 'tags_view_for_resource.php'; // NU TREBUIE REQUIRE_ONCE
            ?>
        </td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['quantity']); ?> <?php echo htmlspecialchars($row['unit']); ?></td>
        <td><?php echo htmlspecialchars($row['description']); ?></td>
        <td>
            <form onsubmit="return confirm('Are you sure you want to remove this resource?');" action="resource/delete.php?inventory_id=<?php echo urlencode($row['inventory_id']);?>&resourceId=<?php echo urlencode($row['id']);?>" method="post">
                <input name="submitRemoveResource" type="submit" value="Delete">
            </form>
        </td>
    </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="5"><a href="new_resource.php?inventory_id=<?php echo urlencode($inventory_id); ?>">Create new resource</a></td>
    </tr>
    </tbody>
</table>
</div>