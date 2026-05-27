<?php
    $inventoryId = $_GET['inventory_id'] ?? NULL;
    $action = is_null($inventoryId) ? "new_currency.php" : ($createCurrencyAction . "?inventory_id=". $inventoryId);
?>
<div class="container new-currency">
    <h2 class="center-content">Create new currency</h2>
    <form action= "<?php echo $action; ?>"  method="post">
        <label for="currencyName">Currency Name: </label>
        <input type="text" name="currencyName" id="currencyName" required> <br>
        <label for="currencyCode">Currency Type: </label>
        <select name="currencyCode" id="currencyCode">
            <option>EUR</option>
            <option>RON</option>
            <option>PLN</option>
        </select>
        <h2> todo: de inclus currency-uri din currencycontroller sau ceva</h2>
        <label for="description">Description: </label>
        <textarea name="description" id="description" maxlength="255" rows="10"> </textarea>
        <?php if (!empty( $message ?? '')): ?>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="submitNewCurrency" value="Submit">
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
