<?php
//
// logout.php
// Ends the user session and redirects to login page
//
session_start();
session_unset();
session_destroy();
header("Location: ../html/login.html");
exit;
?>
