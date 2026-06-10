
<div>
<script>
    <?php require_once "js/toggle_table_contents.js"; ?>
</script>
<h2 class="center-content">Resources</h2>
<form method="get" action="inventory.php" style="margin-bottom: 12px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
    <input type="hidden" name="inventory_id" value="<?php echo htmlspecialchars($inventory['id'] ?? ''); ?>">
    <label for="tag_id">Filter by tag:</label>
    <select name="tag_id" id="tag_id">
        <option value="">All tags</option>
        <?php foreach (($tags ?? array()) as $tag): ?>
            <option value="<?php echo htmlspecialchars($tag['id']); ?>" <?php echo ((string)($selectedTagId ?? '') === (string)$tag['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($tag['name']); ?></option>
        <?php endforeach; ?>
    </select>
    <input type="submit" value="Apply">
    <?php if (!empty($selectedTagId)): ?>
        <a href="inventory.php?inventory_id=<?php echo urlencode($inventory['id'] ?? ''); ?>">Clear</a>
    <?php endif; ?>
</form>
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
        $inventoryTags = $tags ?? array();
        $rows = $inventory_id ? $inventoryController->getResourcesForInventory($currentUser, $inventory_id, $selectedTagId ?? null) : array();
        foreach ($rows as $row):
    ?>
    <tr>
        <td>
            <?php
                $resourceTags = $inventoryService -> getTagsForResource($currentUser, $row['id']);
                require 'tags_view_for_resource.php'; // NU TREBUIE REQUIRE_ONCE
            ?>
        </td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['quantity']); ?> <?php echo htmlspecialchars($row['unit']); ?></td>
        <td><?php echo htmlspecialchars($row['description']); ?></td>
        <td style="display: flex; gap: 5px; flex-wrap: wrap;">
            <a href="resource/transaction.php?inventory_id=<?php echo urlencode($row['inventory_id']);?>&resource_id=<?php echo urlencode($row['id']);?>&operation=add" style="padding: 4px 8px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 3px; font-size: 0.85em;">+</a>
            <a href="resource/transaction.php?inventory_id=<?php echo urlencode($row['inventory_id']);?>&resource_id=<?php echo urlencode($row['id']);?>&operation=subtract" style="padding: 4px 8px; background-color: #ff9800; color: white; text-decoration: none; border-radius: 3px; font-size: 0.85em;">−</a>
            <form onsubmit="return confirm('Are you sure you want to remove this resource?');" action="resource/delete.php?inventory_id=<?php echo urlencode($row['inventory_id']);?>&resourceId=<?php echo urlencode($row['id']);?>" method="post">
                <input name="submitRemoveResource" type="submit" value="Delete" style="padding: 4px 8px; background-color: #f44336; color: white; border: none; border-radius: 3px; font-size: 0.85em; cursor: pointer;">
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