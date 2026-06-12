<div>
<script>
    <?php require_once "js/toggle_table_contents.js"; ?>
</script>
<h2 class="center-content">Notifications</h2>
<table class="notification-table">
    <thead onclick="toggleTableContents(event)">
        <tr>
            <th>When</th>
            <th>Inventory</th>
            <th>Text</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($notifications as $not): ?>
        <tr>
            <td><?php echo htmlspecialchars(date("M d Y", strtotime($not['created_at']))); ?></td>
            <td><?php echo isset($not['inventory_id']) ? '<a href="inventory.php?inventory_id=' . urlencode($not['inventory_id']) . '">Inventory ' . htmlspecialchars($not['inventory_id']) . '</a>' : 'General'; ?></td>
            <td><?php echo nl2br(htmlspecialchars($not['message'])); ?></td>
        </tr>
    <?php endforeach; ?>
    <!-- <tr>
        <td>Dec 25 2025</td>
        <td><a href="inventory.php?inventory_id=1">Inventar 1</a></td>
        <td>Ramanem iminent fara lemne</td>
    </tr>
    <tr>
        <td>Dec 25 2025</td>
        <td><a href="inventory.php?inventory_id=1">Inventar 1</a></td>
        <td>Ramanem iminent fara lemne</td>
    </tr>
    <tr>
        <td>Dec 25 2025</td>
        <td><a href="inventory.php?inventory_id=1">Inventar 1</a></td>
        <td>Ramanem iminent fara lemne</td>
    </tr>
    <tr>
        <td>Dec 25 2025</td>
        <td><a href="inventory.php?inventory_id=1">Inventar 1</a></td>
        <td>Ramanem iminent fara lemne</td>
    </tr> -->
    </tbody>
</table>
</div>