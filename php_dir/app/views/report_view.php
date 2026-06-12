<?php
    require_once "app/models/InventoryModel.php";
    $inventoryModel = new InventoryModel($connection);
    $inventory = $inventoryModel -> getInventoryById($inventoryId);
    echo count($history);
?>
<h2>Report</h2>
<h3><?php echo $inventory['name'];?></h3>
<ol>
<?php foreach($history as $entry):?>
<li> Change: <?php echo $entry['quantity_change'];?> </li>
<?php endforeach; ?>
</ol>


