<?php
    $inventoryId = $_GET['inventory_id'] ?? NULL;
    $inventory = $data['inventory'] ?? null;
    $associates = $data['associates'] ?? array();
    $permissionMasks = $data['permission_masks'] ?? array();
?>
<div class="container full">
    <a href="inventory.php?inventory_id=<?php echo urlencode($inventoryId); ?>">Back to Inventory</a>
    <div class="sep"></div>
    <h2>Manage Access: <?php echo htmlspecialchars($inventory['name'] ?? 'Inventory'); ?></h2>
    
    <?php if (!empty($message)): ?>
        <p class="<?php echo strpos($message, 'successfully') !== false || strpos($message, 'added') !== false || strpos($message, 'removed') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <h3>Users with Access</h3>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Read</th>
                <th>Edit</th>
                <th>Update</th>
                <th>Delete</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($associates as $assoc): 
                $userPerms = intval($assoc['permissions']);
            ?>
            <tr>
                <td><?php echo htmlspecialchars($assoc['username'] ?? 'Unknown'); ?></td>
                <td><?php echo htmlspecialchars($assoc['email'] ?? '-'); ?></td>
                <form method="POST" action="manage_access.php?inventory_id=<?php echo urlencode($inventoryId); ?>&user_id=<?php echo urlencode($assoc['user_id']); ?>" style="display: contents;">
                    <td>
                        <input type="checkbox" name="perm_read" <?php echo ($userPerms & $permissionMasks['read']) ? 'checked' : ''; ?>>
                    </td>
                    <td>
                        <input type="checkbox" name="perm_edit" <?php echo ($userPerms & $permissionMasks['edit']) ? 'checked' : ''; ?>>
                    </td>
                    <td>
                        <input type="checkbox" name="perm_update" <?php echo ($userPerms & $permissionMasks['update']) ? 'checked' : ''; ?>>
                    </td>
                    <td>
                        <input type="checkbox" name="perm_delete" <?php echo ($userPerms & $permissionMasks['delete']) ? 'checked' : ''; ?>>
                    </td>
                    <td>
                        <input type="submit" name="submitUpdateAccess" value="Update">
                        <button type="submit" name="submitRemoveAccess" value="1" style="background-color: #ff6b6b; color: white; border: none; padding: 2px 8px; cursor: pointer;" onclick="return confirm('Remove this user\\'s access?');">Remove</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="sep"></div>
    <h3>Add New User</h3>
    <form method="POST" action="manage_access.php?inventory_id=<?php echo urlencode($inventoryId); ?>">
        <label for="new_username">Username: </label>
        <input type="text" name="new_username" id="new_username" required> <br>
        
        <label>Initial Permissions: </label> <br>
        <label><input type="checkbox" name="new_perm_read" checked> Read</label>
        <label><input type="checkbox" name="new_perm_edit"> Edit</label>
        <label><input type="checkbox" name="new_perm_update"> Update</label>
        <label><input type="checkbox" name="new_perm_delete"> Delete</label> <br>
        
        <input type="submit" name="submitAddUser" value="Add User">
    </form>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
