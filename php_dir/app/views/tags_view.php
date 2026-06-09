<script>
    <?php require_once __DIR__."/../../js/tag.js"; ?>
</script>
<div>
    <h3>Tags</h3>
    <div id="tag-wrapper" class="tag-container">
    <?php if(isset($tags) && is_array($tags)): ?>
        <?php foreach($tags as $tag): ?>
            <script>
                makeTag('tag-wrapper', 
                        '<?php echo htmlspecialchars($tag['name']); ?>', 
                        '<?php echo htmlspecialchars($tag['fgcolor']); ?>', 
                        '<?php echo htmlspecialchars($tag['bgcolor']); ?>', 
                            'delete_tag.php?inventory_id=<?php echo urlencode($inventory['id']); ?>&id=<?php echo urlencode($tag['id']); ?>', 
                        'submitDeleteTag');
            </script>
        <?php endforeach; ?>
    <?php endif; ?>
    <script>
        makeLinkTag('tag-wrapper', 'New', '<?php echo "new_tag.php?inventory_id=".urlencode($inventoryId); ?>', '#000000', '#FFFFFF00');
    </script>
    </div>
</div>