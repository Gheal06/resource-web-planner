<?php
require_once "header.php";

require_once "app/services/AdminService.php";
require_once "app/services/AuthService.php";
require_once "app/controllers/AdminController.php";

if (!$authService->isAdmin($currentUser)) {
  header('Location: index.php');
  exit();
}
$adminController = new AdminController($connection);
$adminService = new AdminService($connection);
$authService = new AuthService($connection);
$data['all_users'] = $adminService -> getAllUsers();
$message = $adminController->adminAction()['message'] ?? '';
$adminUpdateAction = "admin_panel.php";
if (!$authService->isAdmin($currentUser)) {
  header('Location: index.php');
  exit();
}


require_once "app/views/header_view.php";

require_once "app/views/admin_dashboard_view.php";
require_once "footer.php";
?>