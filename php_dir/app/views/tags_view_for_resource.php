<script>
    <?php require_once __DIR__."/../../js/tag.js"; ?>
</script>
<?php $divId='tag-wrapper'.$row['id']; ?>
<div id="<?php echo $divId?>" class="tag-container">
<?php foreach(($resourceTags ?? array()) as $tag): ?>
    <script>
        makeTag('<?php echo $divId?>', 
                '<?php echo $tag['name']?>', 
                '<?php echo $tag['fgcolor']?>', 
                '<?php echo $tag['bgcolor']?>',
                'delete_tag_for_resource.php?inventory_id=<?php echo $inventory['id']?>&resourceId=<?php echo $row['id']; ?>&id=<?php echo $tag['id']; ?>');
    </script>
<?php endforeach; ?>
<form action="new_tag_for_resource.php?inventory_id=<?php echo urlencode($inventory['id']); ?>&resourceId=<?php echo urlencode($row['id']); ?>" method="post" style="margin-top: 8px; display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
    <select name="tag_id" required>
        <option value="" disabled selected>Attach existing tag</option>
        <?php foreach (($inventoryTags ?? array()) as $availableTag): ?>
            <option value="<?php echo htmlspecialchars($availableTag['id']); ?>"><?php echo htmlspecialchars($availableTag['name']); ?></option>
        <?php endforeach; ?>
    </select>
    <input type="submit" name="submitAddTagToResource" value="Add tag">
</form>
</div>