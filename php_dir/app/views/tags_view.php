<script>
    <?php require_once __DIR__."/../../js/tag.js"; ?>
</script>
<div>
    <h3>Tags</h3>
    <div id="tag-wrapper" class="flex">
    <?php foreach($tags as $tag): ?>
        <script>
            makeTag('tag-wrapper', '<?php echo $tag['name']?>', '<?php echo $tag['fgcolor']?>', '<?php echo $tag['bgcolor']?>');
        </script>
    <?php endforeach; ?>
    <script>
        makeLinkTag('tag-wrapper', 'New', '<?php echo "new_tag.php?inventory_id=".$inventoryId; ?>', '#000000', '#FFFFFF');
    </script>
    </div>
<div>