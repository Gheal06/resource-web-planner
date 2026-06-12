<?php
require_once "header.php";

require_once "app/services/AdminService.php";
require_once "app/services/AuthService.php";
require_once "app/views/header_view.php";

$adminService = new AdminService($connection);
$authService = new AuthService($connection);
$data['all_users'] = $adminService -> getAllUsers();

require_once "app/views/admin_dashboard_view.php";
require_once "footer.php";
?>