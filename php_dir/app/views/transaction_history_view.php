<div>
    <script>
        <?php require_once "js/toggle_table_contents.js"; ?>
    </script>
    <h2 class="center-content">Transaction History</h2>
    <table id="history-table">
        <thead onclick="toggleTableContents(event)">
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
        <tr>
            <td>instalator</td>
            <td>Vine instalatorul sa repare ac-ul</td>
            <td>Tue 26 May 2026, 14:00</td>
        </tr>
        <tr>
            <td>Lemn</td>
            <td>Gigel vine cu lemnul</td>
            <td>Tue 26 May 2026, 14:00</td>
        </tr>
        <tr>
            <td>salarii</td>
            <td>Salarii</td>
            <td>01 June 2026, 00:00</td>
        </tr>
        <tr>
            <td colspan="3"><a href="new_transaction.php?inventoryId=<?php echo urlencode($inventory_id); ?>">Create new transaction</a></td>
            <!-- de pus id-ul inventory-ului in linkul de mai sus -->
        </tr>
        <!-- de inserat toate tranzactiile upcoming-->
        </tbody>
    </table>
</div>