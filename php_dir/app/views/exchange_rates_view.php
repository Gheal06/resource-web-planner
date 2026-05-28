<?php
    $hasRates = is_array($exchangeRates) && !empty($exchangeRates);
?>
<div class="container full exchange-page">
    <h2 class="center-content">Exchange rates</h2>
    <form class="exchange-form" action="exchange_rates.php" method="get">
        <label for="currency_code">Currency code:</label>
        <div class="exchange-form-row">
            <input type="text" name="currency_code" id="currency_code" maxlength="3" minlength="3" required value="<?php echo htmlspecialchars($requestedCode ?? ''); ?>" placeholder="USD">
            <input type="submit" value="Check rates">
        </div>
    </form>

    <?php if (!empty($rateError)): ?>
        <p class="error"><?php echo htmlspecialchars($rateError); ?></p>
    <?php elseif ($hasRates): ?>
        <p class="exchange-summary">Showing rates for <strong><?php echo htmlspecialchars($requestedCode); ?></strong>. Sell rates come from the exchange-rate API; buy rates are derived as the reciprocal.</p>
        <table id="exchange-rates-table" class="exchange-rates-table">
            <thead>
                <tr>
                    <th>Currency</th>
                    <th>Buy</th>
                    <th>Sell</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exchangeRates as $currencyCode => $sellRate): ?>
                    <?php
                        $sellRateValue = is_numeric($sellRate) ? (float)$sellRate : 0.0;
                        $buyRateValue = $sellRateValue > 0 ? 1 / $sellRateValue : 0.0;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($currencyCode); ?></td>
                        <td><?php echo number_format($buyRateValue, 6, '.', ''); ?></td>
                        <td><?php echo number_format($sellRateValue, 6, '.', ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($requestedCode !== ''): ?>
        <p>Please try another currency code.</p>
    <?php else: ?>
        <p>Enter a currency code to load the available exchange rates.</p>
    <?php endif; ?>
</div>