<?php

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/app/models/FonduriModel.php";
require_once __DIR__ . "/app/models/FonduriTransactionHistoryModel.php";
require_once __DIR__ . "/app/controllers/InventoryManagementController.php";
require_once __DIR__ . "/app/services/InventoryManagementService.php";
$inventory_id = $_GET['inventory_id'] ?? $_POST['inventory_id'] ?? null;
$fonduri_id = $_GET['fonduri_id'] ?? $_POST['fonduri_id'] ?? null;
$operation = $_GET['operation'] ?? $_POST['operation'] ?? null; // 'add' or 'subtract'
$amount = $_GET['amount'] ?? $_POST['amount'] ?? null;
$description = $_GET['description'] ?? $_POST['description'] ?? null;

$success = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Display form for add/subtract operation
    if (!$fonduri_id || !$inventory_id || !$operation) {
        header('Location: inventory.php?inventory_id=' . urlencode($inventory_id));
        exit;
    }

    $fonduriModel = new FonduriModel($connection);
    $fonduri = $fonduriModel->getById($fonduri_id);
    
    if (!$fonduri) {
        echo "Fund not found";
        exit;
    }

    // Check if user has access
    $inventoryController = new InventoryManagementController($connection);
    $inventory = $inventoryController->getUserInventoryById($inventory_id);
    
    if (!$inventory) {
        echo "You don't have access to this inventory";
        exit;
    }

    $operationLabel = $operation === 'add' ? 'Add' : 'Subtract';
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title><?php echo htmlspecialchars($operationLabel); ?> Fund Amount</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
    <?php require_once __DIR__ . "/app/views/header_view.php"; ?>
    <div class="container">
        <h2><?php echo htmlspecialchars($operationLabel); ?> Amount for <?php echo htmlspecialchars($fonduri['name'] ?? $fonduri['currency_code']); ?></h2>
        <p>Current Amount: <?php echo htmlspecialchars($fonduri['amount']); ?> <?php echo htmlspecialchars($fonduri['currency_code']); ?></p>
        
        <form method="POST">
            <input type="hidden" name="inventory_id" value="<?php echo htmlspecialchars($inventory_id); ?>">
            <input type="hidden" name="fonduri_id" value="<?php echo htmlspecialchars($fonduri_id); ?>">
            <input type="hidden" name="operation" value="<?php echo htmlspecialchars($operation); ?>">
            
            <label for="amount">Amount to <?php echo htmlspecialchars($operation); ?>:</label>
            <?php if ($operation === 'subtract'): ?>
              <p style="color: red;">Note: You cannot subtract more than the current amount.</p>
              <input type="number" name="amount" id="amount" step="0.01" min="0.00" max="<?php echo htmlspecialchars($fonduri['amount']); ?>" required>
            <?php else: ?>
              <input type="number" name="amount" id="amount" step="0.01" min="0.00" required>
            <?php endif; ?>
            
            <label for="description">Description (optional):</label>
            <input type="text" name="description" id="description" placeholder="e.g., Donation, Expense, Budget allocation, etc.">
            
            <input type="submit" value="<?php echo htmlspecialchars($operationLabel); ?>">
            <a href="inventory.php?inventory_id=<?php echo urlencode($inventory_id); ?>">Cancel</a>
        </form>
    </div>
    <?php require_once __DIR__ . "/footer.php"; ?>
    </body>
    </html>
    <?php

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the add/subtract operation
    
    if (!$fonduri_id || !$inventory_id || !$operation || $amount === null) {
        header('Location: inventory.php?inventory_id=' . urlencode($inventory_id));
        exit;
    }

    $fonduriModel = new FonduriModel($connection);
    $fonduri = $fonduriModel->getById($fonduri_id);
    
    if (!$fonduri) {
        die('Fund not found');
    }

    $inventoryController = new InventoryManagementController($connection);
    $inventory = $inventoryController->getUserInventoryById($inventory_id);
    
    if (!$inventory) {
        die('You don\'t have access to this inventory');
    }



    $amount = floatval($amount);

    
    if ($operation !== 'add' && $operation !== 'subtract') {
        die('Invalid operation');
    }

    $old_amount = floatval($fonduri['amount']);
    $amount_change = $operation === 'add' ? $amount : -$amount;
    $new_amount = $old_amount + $amount_change;

    if ($new_amount < 0) {
        die('Operation would result in negative amount');
    }

    $inventoryService = new InventoryManagementService($connection);
    $authController = new AuthController($connection);
    $currentUser = $authController->getCurrentUser();
    $inventoryService->setFonduri($currentUser, $inventory_id, $new_amount, $fonduri['currency_code'], $fonduri['name'] ?? null, $description ?? null);
    header('Location: inventory.php?inventory_id=' . urlencode($inventory_id));
    exit;
}

?>
