<?php
session_start();
session_destroy(); // Destroy all session data
header("Location: login.php"); // Redirect the user back to the login page
exit();
?>
