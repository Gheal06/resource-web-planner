<?php
    require_once "app/models/InventoryModel.php";
    $inventoryModel = new InventoryModel($connection);
    $inventory = $inventoryModel -> getInventoryById($inventoryId);
?>
<h2>Report</h2>
<h3><?php echo $inventory['name'];?></h3>
<h4>
<?php 
$qtyChangeStr;
if($tp == 'f'){
    $qtyChangeStr = 'amount_change';
    echo "Fund: " . $resource['currency_code'] . (strlen($resource['name'] ?? '')>0 ? '(' . $resource['name'] . ')': '');
}
else{
    $qtyChangeStr = 'quantity_change';
    echo "Resource: " . $resource['name'];
}
$endingBalance = floatval($startingBalance);
$minBalance = $startingBalance;
$maxBalance = $startingBalance;
?>
</h4>
<p>History:</p>
<ol>
<?php foreach($history as $entry):?>
<li> 
    <?php 
        $delta = floatval($entry[$qtyChangeStr]);
        if($entry['operation_type'] === 'Subtract')
            $delta = -$delta;
        $endingBalance += $delta;
        $minBalance = min($endingBalance, $minBalance);
        $maxBalance = max($endingBalance, $maxBalance);
        echo date('D, d M Y H:i', strtotime($entry['created_at'])) . ' -> ' . $delta;
    ?>
</li>
<?php endforeach; ?> 
</ol>

<p>Starting Balance: <?php echo $startingBalance; ?></p>
<p>Ending Balance: <?php echo $endingBalance; ?></p>
<p>Change: <?php echo $endingBalance - $startingBalance; ?></p>
<p>Min: <?php echo $minBalance; ?></p>
<p>Max: <?php echo $maxBalance; ?></p>


