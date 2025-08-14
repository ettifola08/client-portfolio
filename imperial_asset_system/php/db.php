<?php
//
// db.php
// Database connection file
//

$servername = "localhost"; // Change if your database is on a different host
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password
$dbname = "imperial_asset_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
