<?php
require_once "header.php";
require_once "app/controllers/InventoryManagementController.php";
require_once "app/views/header_view.php";

$inventoryController = new InventoryManagementController($connection);
$inventoryId = $_GET['inventory_id'] ?? null;
$message = '';

verifyAccess($inventoryId, EDIT);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitUpdateAccess']) && isset($_GET['user_id'])) {
    $target_user_id = $_GET['user_id'];
    $newPerms = 0;
    if (isset($_POST['perm_read'])) $newPerms |= 1;
    if (isset($_POST['perm_edit'])) $newPerms |= 2;
    if (isset($_POST['perm_update'])) $newPerms |= 4;
    if (isset($_POST['perm_delete'])) $newPerms |= 8;

    $result = $inventoryController->updateUserAccess($currentUser, $inventoryId, $target_user_id, $newPerms);
    if (is_array($result) && !empty($result['success'])) {
        $message = $result['message'] ?? 'Permissions updated.';
    } else {
        $message = is_array($result) ? ($result['message'] ?? 'Failed to update permissions.') : 'Failed to update permissions.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitRemoveAccess']) && isset($_GET['user_id'])) {
    $target_user_id = $_GET['user_id'];
    $result = $inventoryController->removeUserAccess($currentUser, $inventoryId, $target_user_id);
    if (is_array($result) && !empty($result['success'])) {
        $message = $result['message'] ?? 'User access removed.';
    } else {
        $message = is_array($result) ? ($result['message'] ?? 'Failed to remove user access.') : 'Failed to remove user access.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitAddUser'])) {
    $target_username = trim($_POST['new_username'] ?? '');
    if (empty($target_username)) {
        $message = 'Please enter a username.';
    } else {
        $newPerms = 0;
        if (isset($_POST['new_perm_read'])) $newPerms |= 1;
        if (isset($_POST['new_perm_edit'])) $newPerms |= 2;
        if (isset($_POST['new_perm_update'])) $newPerms |= 4;
        if (isset($_POST['new_perm_delete'])) $newPerms |= 8;

        $result = $inventoryController->addUserAccess($currentUser, $inventoryId, $target_username, $newPerms);
        if (is_array($result) && !empty($result['success'])) {
            $message = $result['message'] ?? 'User access added.';
        } else {
            $message = is_array($result) ? ($result['message'] ?? 'Failed to add user access.') : 'Failed to add user access.';
        }
    }
}

$data = $inventoryController->getManageAccessData($currentUser, $inventoryId);
if (is_array($data) && !empty($data['success'])) {
    require_once "app/views/manage_access_view.php";
} else {
    $message = is_array($data) ? ($data['message'] ?? 'Unable to access inventory.') : 'Unable to access inventory.';
    require_once "app/views/error_view.php";
}
?>
