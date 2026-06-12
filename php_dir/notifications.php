<?php
require_once "header.php";
require_once "conn.php";
require_once "app/services/NotificationService.php";
require_once "app/views/header_view.php";
?>
    <div class="container full">
        <?php
        $notificationService = new NotificationService($connection);
        $notifications = $notificationService->getNotificationsForUsername($currentUser);
        require_once "app/views/notifications_view.php"; ?>
    </div>
<?php require_once "footer.php"; ?>