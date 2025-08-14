<?php
//
// add_client.php
// Adds a new client to the database
//
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../html/login.html"); exit; }

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $joined_date = date("Y-m-d");

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO clients (name, email, contact, joined_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $contact, $joined_date);

    if ($stmt->execute()) {
        header("Location: ../html/clients.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
