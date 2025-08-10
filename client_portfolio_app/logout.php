<?php
// logout.php
// Destroys the current client session and redirects to the login page.
session_start();
session_unset();
session_destroy();
header("Location: index.html");
exit();
?>
