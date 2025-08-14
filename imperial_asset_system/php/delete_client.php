<?php
//
// delete_client.php
// Deletes a client and their investments from the database
//
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../html/login.html");
    exit;
}

include 'db.php';

if (isset($_GET['id'])) {
    $client_id = $_GET['id'];

    // Delete client's investments first due to foreign key constraint
    $stmt_inv = $conn->prepare("DELETE FROM investments WHERE client_id = ?");
    $stmt_inv->bind_param("i", $client_id);
    $stmt_inv->execute();
    $stmt_inv->close();

    // Now delete the client
    $stmt_client = $conn->prepare("DELETE FROM clients WHERE client_id = ?");
    $stmt_client->bind_param("i", $client_id);
    
    if ($stmt_client->execute()) {
        header("Location: ../html/clients.html");
    } else {
        echo "Error: " . $stmt_client->error;
    }

    $stmt_client->close();
    $conn->close();
}
?>
