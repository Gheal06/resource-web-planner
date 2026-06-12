<?php
    $startId = $_GET['start'] ?? 0;
    $cnt = $_GET['cnt'] ?? 10;
    $allUsers = $data['all_users'] ?? array();
?>
<div class="container full">
    <h2>Database operations</h2>
    <form action="" method="post">
        <input name="reset-db" type="submit" value="Reset Database">
        <input name="update-db-procedures" type="submit" value="Update Database Procedures">
    </form>
    
    <div class="sep"></div>
    <h3>Users:</h3>
    
    <?php if (!empty($message ?? '')): ?>
        <p class="<?php echo strpos($message, 'successfully') !== false || strpos($message, 'added') !== false || strpos($message, 'removed') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Id</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allUsers as $user): 
                /*
                    CREATE TABLE users (
                        id                    BIGSERIAL PRIMARY KEY,
                        username              VARCHAR(255) UNIQUE NOT NULL,
                        email                 VARCHAR(255) UNIQUE NOT NULL, 
                        password_hash         VARCHAR(64) NOT NULL
                    );
                */
                $userId = $user['id'] ?? '';
                $username = $user['username'] ?? '-';
                $email = $user['email'] ?? '-';
                $isAdmin = $authService->isAdminById($userId);
            ?>
            <tr>
                <td><?php echo htmlspecialchars($userId); ?></td>
                <td><?php echo htmlspecialchars($username); ?></td>
                <td><?php echo htmlspecialchars($email); ?></td>
                <td>
                    <form method="post" action="">
                        <label for="is-admin">Admin: </label>
                        <input type="checkbox" name="is-admin" id="is-admin" <?php if($isAdmin){echo 'checked';} ?>>
                        <input type="submit" name="submit-update-role" value="Update Role">
                        <input type="submit" name="submit-delete-user" value="Delete User">
                    </form>
                </td>
                
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
