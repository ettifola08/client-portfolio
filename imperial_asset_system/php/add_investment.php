<?php
//
// add_investment.php
// Adds a new investment to the database
//
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../html/login.html"); exit; }

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_POST['client_id'];
    $investment_type = $_POST['investment_type'];
    $amount_invested = $_POST['amount_invested'];
    $current_value = $_POST['current_value'];
    $date_invested = date("Y-m-d");

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO investments (client_id, investment_type, amount_invested, current_value, date_invested) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdds", $client_id, $investment_type, $amount_invested, $current_value, $date_invested);

    if ($stmt->execute()) {
        header("Location: ../html/investments.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
