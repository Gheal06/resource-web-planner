<?php 
  session_start();
  $_SESSION = array(); // Clear all session data
  session_destroy();
  header("Location: index.php");
  exit();
?>