<?php
// add_goal.php
// Handles the submission of a new financial goal from the dashboard.

session_start();
header('Content-Type: application/json');

include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['user_id'];

// Check if all required fields are set
if (isset($_POST['goal_name'], $_POST['target_amount'], $_POST['end_date'])) {
    $goalName = $_POST['goal_name'];
    $targetAmount = $_POST['target_amount'];
    $endDate = $_POST['end_date'];
    $savedAmount = 0; // Initialize saved amount to 0 for a new goal

    $stmt = $conn->prepare("INSERT INTO financial_goals (user_id, goal_name, target_amount, saved_amount, end_date) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param("isids", $userId, $goalName, $targetAmount, $savedAmount, $endDate);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Financial goal added successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add goal: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete data provided.']);
}

$conn->close();