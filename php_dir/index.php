<?php

require_once "header.php";

{
  if ($currentUser) {
      $inventoryTableIDs = $inventoryController->getUserReadableInventoryTables($currentUser);
      $view = 'app/views/dashboard_view.php';
  }
}

require_once __DIR__ . '/app/views/header_view.php';

if ($view) {
    require $view;
} else {
    echo '<div class="container"><p>Welcome. Please <a href="index.php?action=login">login</a> or <a href="index.php?action=register">register</a>.</p></div>';
}

require_once "footer.php";

?>