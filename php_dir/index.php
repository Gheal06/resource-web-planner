<?php

require_once "header.php";

{
  if ($currentUser) {
      $inventoryTableIDs = $inventoryController->getUserReadableInventories($currentUser);
      $view = 'app/views/dashboard_view.php';
  }
}

require_once __DIR__ . '/app/views/header_view.php';

if ($view) {
    require $view;
} else {
    require 'app/views/logged_out_view.php';
}

require_once "footer.php";

?>