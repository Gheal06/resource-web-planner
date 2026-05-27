<?php
    $inventoryId = $_GET['inventory_id'] ?? NULL;
    $action = is_null($inventoryId) ? "new_tag.php" : ($createTagAction . "?inventory_id=". $inventoryId);
?>
<script>
    <?php require_once __DIR__."/../../js/tag.js"; ?>
</script>
<div class="container new-inventory">
    <h2 class="center-content">Create new tag</h2>
    <form action="<?php echo "$action"?>"  method="post">
        <label for="tag-name" >Tag Name: </label>
        <input type="text" name="tag-name" id="tag-name" value="New Tag" required oninput="renderTag()"> <br>
        <label for="bgcolor">Background Color: </label>
        <input type="color" name="bgcolor" id="bgcolor" value="#DDDDFF" required oninput="renderTag()"> <br>
        <label for="fgcolor">Text Color: </label>
        <input type="color" name="fgcolor" id="fgcolor" value="#000000" required oninput="renderTag()"> <br>

        <div class="flex" style="display:none"; id="tag-output-wrapper">
            <div>
            <span>Result: </span> 
            </div>
            <div class="tag" id="tag-output">
                <span id="tag-output-text" onload="renderTag()"></span>
            </div>
        </div>
        <?php if (!empty( $message ?? '')): ?>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="submitNewTag" value="Submit">
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>