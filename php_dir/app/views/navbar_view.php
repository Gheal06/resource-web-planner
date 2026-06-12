<div id="nav" class="flex">
        <div id="nav-left">
            <img src="favicon.ico" alt="">
            <a href="index.php">Home</a>
            <a href="exchange_rates.php">Exchange Rates</a>
            <?php if(isset($currentUser) && $authService->isAdmin($currentUser)): ?>
                <a href="admin_panel.php" class="centerY">Admin Dashboard</a>
            <?php endif; ?>
        </div>
        <div id="nav-right">
            <?php if(!$currentUser): ?>
              <a href="login.php" class="centerY">Login</a>
              <a href="register.php" class="centerY">Register</a>
            <?php else: ?>
              <img class="gravatar-image centerY" src="<?php echo $gravatarService->getGravatarUrl($currentUser); ?>" alt="">
              <a href="account.php" class="centerY"><?php echo htmlspecialchars($currentUser);?></a>
              <a href="notifications.php">🔔</a>
            <a href="logout.php" class="centerY">Logout</a>
            <?php endif; ?>
        </div>
    </div>