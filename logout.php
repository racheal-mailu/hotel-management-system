<?php
session_start();

// Destroy all session data
$_SESSION = [];
session_destroy();

// Redirect to homepage with customer and admin portal
header("Location: index.php");
exit();
?>
