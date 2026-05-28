<script>
    <?php require_once __DIR__."/../../js/tag.js"; ?>
</script>
<?php $divId='tag-wrapper'.$row['id']; ?>
<div id="<?php echo $divId?>" class="tag-container">
<?php foreach($tags as $tag): ?>
    <script>
        makeTag('<?php echo $divId?>', '<?php echo $tag['name']?>', '<?php echo $tag['fgcolor']?>', '<?php echo $tag['bgcolor']?>');
    </script>
<?php endforeach; ?>
<script>
    makeLinkTag('<?php echo $divId?>', 'Add tag', '<?php echo "new_tag.php?inventory_id=".urlencode($inventoryId); ?>', '#000000', '#FFFFFF00');
</script>
</div>