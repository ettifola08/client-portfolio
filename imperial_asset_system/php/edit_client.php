<?php
//
// edit_client.php
// Updates an existing client in the database
//
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../html/login.html"); exit; }

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_POST['client_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE clients SET name = ?, email = ?, contact = ? WHERE client_id = ?");
    $stmt->bind_param("sssi", $name, $email, $contact, $client_id);

    if ($stmt->execute()) {
        header("Location: ../html/clients.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
