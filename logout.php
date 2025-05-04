<?php
// Inside logout.php
session_start();
session_unset(); // Clear session variables
session_destroy(); // Destroy the session
header('Location: login.php'); // Redirect
exit();

?>
