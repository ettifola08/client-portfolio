<?php
//
// edit_investment.php
// Updates an existing investment in the database
//
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../html/login.html"); exit; }

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $investment_id = $_POST['investment_id'];
    $client_id = $_POST['client_id'];
    $investment_type = $_POST['investment_type'];
    $amount_invested = $_POST['amount_invested'];
    $current_value = $_POST['current_value'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE investments SET client_id = ?, investment_type = ?, amount_invested = ?, current_value = ? WHERE investment_id = ?");
    $stmt->bind_param("isddi", $client_id, $investment_type, $amount_invested, $current_value, $investment_id);

    if ($stmt->execute()) {
        header("Location: ../html/investments.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
