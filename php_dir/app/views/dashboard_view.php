<?php require_once __DIR__ . '/../../header.php'; ?>
<div class="container">
    <h2>Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($currentUser ?? ''); ?>!</p>
    <p><a href="index.php?action=logout">Logout</a></p>
</div>
<?php require_once __DIR__ . '/../../footer.php'; ?>
