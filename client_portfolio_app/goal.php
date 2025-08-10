<?php
// add_goal.php
// Handles setting a new financial goal for a client.
//
// This script is called via an AJAX request from the dashboard.

session_start();
include 'db_connect.php';

header('Content-Type: application/json');

// Check if client is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication failed. Please log in.']);
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $goalName = trim($_POST['goal_name']);
    $targetAmount = floatval($_POST['target_amount']);
    $endDate = $_POST['end_date'];

    // Insert new goal into the database
    $stmt = $conn->prepare("INSERT INTO financial_goals (user_id, goal_name, target_amount, end_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $userId, $goalName, $targetAmount, $endDate);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Your financial goal has been set!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to set goal. Please try again.']);
    }

    $stmt->close();
    $conn->close();
}
?>
