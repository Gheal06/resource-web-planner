<?php
    require_once "app/models/InventoryModel.php";
    $inventoryModel = new InventoryModel($connection);
    $inventory = $inventoryModel -> getInventoryById($inventoryId);
?>
<h2>Report</h2>
<h3><?php echo $inventory['name'];?></h3>
<h4>
<?php 
if($tp == 'f'){
    echo "Fund: " . $resource['currency_code'] . (strlen($resource['name'] ?? '')>0 ? '(' . $resource['name'] . ')': '');
}
else{
    echo "Resource: " . $resource['name'];
}
?>
</h4>
<ol>
<?php foreach($history as $entry):?>
<li> 
    <?php 
        $delta = $entry['amount_change'];
        settype($delta, "integer");
        if($entry['operation_type'] === 'Subtract')
            $delta = -$delta;
        echo date('D, d M Y H:i', strtotime($entry['created_at'])) . ' -> ' . $delta;
    ?>
</li>
<?php endforeach; ?> 
</ol>


