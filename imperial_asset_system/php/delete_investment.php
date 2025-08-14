<?php
//
// delete_investment.php
// Deletes an investment from the database
//
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../html/login.html");
    exit;
}

include 'db.php';

if (isset($_GET['id'])) {
    $investment_id = $_GET['id'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM investments WHERE investment_id = ?");
    $stmt->bind_param("i", $investment_id);
    
    if ($stmt->execute()) {
        header("Location: ../html/investments.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
