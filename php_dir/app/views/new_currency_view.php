
<div class="container new-currency">
    <h2 class="center-content">Create new currency</h2>
    <form action= "<?php echo $createCurrencyAction; ?>"  method="post">
        <label for="currency-name">Currency Name: </label>
        <input type="text" name="currency-name" required> <br>
        <label for="currency-type">Currency Type: </label>
        <select name="currency-type">
            <option>EUR</option>
            <option>RON</option>
            <option>PLN</option>
        </select>
        <h2> todo: de inclus currency-uri din currencycontroller sau ceva</h2>
        <label for="description">Description: </label>
        <textarea name="description" maxlength="255" rows="10"> </textarea>
        <?php if (!empty( $message ?? '')): ?>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <div style="text-align:center; margin-top:10px;">
            <input type="submit" name="submit" value="Submit">
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
