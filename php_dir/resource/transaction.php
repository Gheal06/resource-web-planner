<?php

require_once __DIR__ . "/../header.php";
require_once __DIR__ . "/../app/models/ResurseModel.php";
require_once __DIR__ . "/../app/models/ResourceTransactionHistoryModel.php";
require_once __DIR__ . "/../app/controllers/InventoryManagementController.php";
require_once __DIR__ . "/../app/services/InventoryManagementService.php";

$css = __DIR__ . "/../index.css";

$inventory_id = $_GET['inventory_id'] ?? $_POST['inventory_id'] ?? null;
$resource_id = $_GET['resource_id'] ?? $_POST['resource_id'] ?? null;
$operation = $_GET['operation'] ?? $_POST['operation'] ?? null; // 'add' or 'subtract'
$quantity = $_GET['quantity'] ?? $_POST['quantity'] ?? null;
$description = $_GET['description'] ?? $_POST['description'] ?? null;

$success = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Display form for add/subtract operation
    if (!$resource_id || !$inventory_id || !$operation) {
        header('Location: ../inventory.php?inventory_id=' . urlencode($inventory_id));
        exit;
    }

    $resourceModel = new ResurseModel($connection);
    $resource = $resourceModel->getById($resource_id);
    
    if (!$resource) {
        echo "Resource not found";
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
        <title><?php echo htmlspecialchars($operationLabel); ?> Resource Quantity</title>
        <link rel="stylesheet" href="../index.css">
    </head>
    <body>
    <?php require_once __DIR__ . "/../app/views/header_view.php"; ?>
    <div class="container">
        <h2><?php echo htmlspecialchars($operationLabel); ?> Quantity for <?php echo htmlspecialchars($resource['name']); ?></h2>
        <p>Current Quantity: <?php echo htmlspecialchars($resource['quantity']); ?> <?php echo htmlspecialchars($resource['unit']); ?></p>
        
        <form method="POST" onsubmit="return confirm('Are you sure?');">
            <input type="hidden" name="inventory_id" value="<?php echo htmlspecialchars($inventory_id); ?>">
            <input type="hidden" name="resource_id" value="<?php echo htmlspecialchars($resource_id); ?>">
            <input type="hidden" name="operation" value="<?php echo htmlspecialchars($operation); ?>">
            
            <label for="quantity">Quantity to <?php echo htmlspecialchars($operation); ?>:</label>
            <input type="number" name="quantity" id="quantity" step="0.01" required>
            
            <label for="description">Description (optional):</label>
            <input type="text" name="description" id="description" placeholder="e.g., Initial stock, Donation, etc.">
            
            <input type="submit" value="<?php echo htmlspecialchars($operationLabel); ?>">
            <a href="../inventory.php?inventory_id=<?php echo urlencode($inventory_id); ?>">Cancel</a>
        </form>
    </div>
    <?php require_once __DIR__ . "/../footer.php"; ?>
    </body>
    </html>
    <?php

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the add/subtract operation
    
    if (!$resource_id || !$inventory_id || !$operation || $quantity === null) {
        header('Location: ../inventory.php?inventory_id=' . urlencode($inventory_id));
        exit;
    }

    $resourceModel = new ResurseModel($connection);
    $resource = $resourceModel->getById($resource_id);
    
    if (!$resource) {
        die('Resource not found');
    }

    // Check if user has access
    $inventoryController = new InventoryManagementController($connection);
    $inventory = $inventoryController->getUserInventoryById($inventory_id);
    
    if (!$inventory) {
        die('You don\'t have access to this inventory');
    }

    $quantity = floatval($quantity);
    
    // Validate operation
    if ($operation !== 'add' && $operation !== 'subtract') {
        die('Invalid operation');
    }

    // Calculate new quantity
    $old_quantity = floatval($resource['quantity']);
    $quantity_change = $operation === 'add' ? $quantity : -$quantity;
    $new_quantity = $old_quantity + $quantity_change;

    // Check if new quantity would be negative
    if ($new_quantity < 0) {
        die('Operation would result in negative quantity');
    }

    // Update resource quantity
    $updateSql = "UPDATE resources SET quantity = $1 WHERE id = $2";
    $updateRes = @pg_query_params($connection, $updateSql, array($new_quantity, $resource_id));
    
    if ($updateRes === false) {
        die('Error updating resource: ' . pg_last_error($connection));
    }

    // Record transaction
    $resourceHistoryModel = new ResourceTransactionHistoryModel($connection);
    $txResult = $resourceHistoryModel->addTransaction(
        $resource_id,
        $resource['name'],
        $inventory_id,
        $operation,
        $quantity_change,
        $old_quantity,
        $new_quantity,
        $description,
        $currentUser ? $authController->getUserByUsername($currentUser)['id'] ?? null : null
    );

    if ($txResult['success']) {
        header('Location: ../inventory.php?inventory_id=' . urlencode($inventory_id) . '&success=' . urlencode($operation === 'add' ? 'Added' : 'Subtracted') . ' quantity');
    } else {
        die('Error recording transaction: ' . $txResult['message']);
    }
    exit;
}

?>
