<?php
    require_once "app/models/InventoryModel.php";
    $inventoryModel = new InventoryModel($connection);
    $inventory = $inventoryModel -> getInventoryById($inventoryId);
    $startDate = ($startDate == '-infinity' ? '-∞' : date('d-m-y h:i A', strtotime($startDate)));
    $endDate = ($endDate == 'infinity' ? '+∞' : date('d-m-y h:i A', strtotime($endDate.'-1 second')));
    $thresholdAmount = $resource['threshold_amount'] ?? 0;
?>
<h2>Report (<?php echo htmlspecialchars($startDate.' -> '.$endDate); ?>)</h2>
<h3><?php echo htmlspecialchars($inventory['name']);?></h3>
<h4>
<?php 
$qtyChangeStr;
if($tp == 'f'){
    $qtyChangeStr = 'amount_change';
    echo htmlspecialchars("Fund: " . $resource['currency_code'] . (strlen($resource['name'] ?? '')>0 ? ' (' . $resource['name'] . ')': ''));
}
else{
    $qtyChangeStr = 'quantity_change';
    echo htmlspecialchars("Resource: " . $resource['name']);
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
    <td> <?php echo htmlspecialchars($i++); ?> </td>
    <td> <?php echo htmlspecialchars(date('D, d M Y H:i', strtotime($entry['created_at']))); ?> </td>
    <td> <?php echo htmlspecialchars($delta.' '.$unit); ?> </td>
    <td> <?php echo htmlspecialchars($endingBalance-$delta.' '.$unit); ?> </td>
    <td> <?php echo htmlspecialchars($endingBalance.' '.$unit); ?> </td>
</tr>
<?php endforeach; ?> 
</tbody>
</table>

<ul>
<li>Total Transactions: <?php echo htmlspecialchars(count($history)); ?></li>
<li>Starting Balance: <?php echo htmlspecialchars($startingBalance.' '.$unit); ?></li>
<li>Ending Balance: <?php echo htmlspecialchars($endingBalance.' '.$unit); ?></li>
<li>Overall Change: <?php echo htmlspecialchars($endingBalance - $startingBalance.' '.$unit); ?></li>
<li>Min Balance: <?php echo htmlspecialchars($minBalance.' '.$unit); ?></li>
<li>Max Balance: <?php echo htmlspecialchars($maxBalance.' '.$unit); ?></li>
<li>Critical Balance: <?php echo htmlspecialchars($thresholdAmount.' '.$unit); ?></li>
</ul>


