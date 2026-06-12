<?php
    require_once "app/models/InventoryModel.php";
    $inventoryModel = new InventoryModel($connection);
    $inventory = $inventoryModel -> getInventoryById($inventoryId);
    $startDate = ($startDate == '-infinity' ? '-∞' : date('d-m-y h:i A', strtotime($startDate)));
    $endDate = ($endDate == 'infinity' ? '+∞' : date('d-m-y h:i A', strtotime($endDate.'-1 second')));
    $thresholdAmount = $resource['threshold_amount'] ?? 0;
?>
<h2>Report (<?php echo $startDate.' -> '.$endDate; ?>)</h2>
<h3><?php echo $inventory['name'];?></h3>
<h4>
<?php 
$qtyChangeStr;
if($tp == 'f'){
    $qtyChangeStr = 'amount_change';
    echo "Fund: " . $resource['currency_code'] . (strlen($resource['name'] ?? '')>0 ? ' (' . $resource['name'] . ')': '');
}
else{
    $qtyChangeStr = 'quantity_change';
    echo "Resource: " . $resource['name'];
}
$endingBalance = floatval($startingBalance);
$minBalance = $startingBalance;
$maxBalance = $startingBalance;
$i = 1;

$unit = $tp == 'f' ? ($resource['currency_code'] ?? '') : ($resource['unit'] ?? '');

?>
</h4>
<center>
    <p><b>Transactions</b><p>
</center>
<table>
<thead>
    <tr>
        <th>Number</th>
        <th>Date</th>
        <th>Delta</th>
        <th>Starting Balance</th>
        <th>Ending Balance</th>
    </tr>
</thead>
<tbody>
<?php foreach($history as $entry):?>
<tr> 
    <?php 
        $delta = floatval($entry[$qtyChangeStr]);
        if($entry['operation_type'] === 'Subtract')
            $delta = -$delta;
        $endingBalance += $delta;
        $minBalance = min($endingBalance, $minBalance);
        $maxBalance = max($endingBalance, $maxBalance);
        // echo date('D, d M Y H:i', strtotime($entry['created_at'])) . ' -> ' . $delta;
    ?>
    <td> <?php echo $i++; ?> </td>
    <td> <?php echo date('D, d M Y H:i', strtotime($entry['created_at'])); ?> </td>
    <td> <?php echo $delta.' '.$unit; ?> </td>
    <td> <?php echo $endingBalance-$delta.' '.$unit; ?> </td>
    <td> <?php echo $endingBalance.' '.$unit; ?> </td>
</tr>
<?php endforeach; ?> 
</tbody>
</table>

<ul>
<li>Total Transactions: <?php echo count($history); ?></li>
<li>Starting Balance: <?php echo $startingBalance.' '.$unit; ?></li>
<li>Ending Balance: <?php echo $endingBalance.' '.$unit; ?></li>
<li>Overall Change: <?php echo $endingBalance - $startingBalance.' '.$unit; ?></li>
<li>Min Balance: <?php echo $minBalance.' '.$unit; ?></li>
<li>Max Balance: <?php echo $maxBalance.' '.$unit; ?></li>
<li>Critical Balance: <?php echo $thresholdAmount.' '.$unit; ?></li>
</ul>


