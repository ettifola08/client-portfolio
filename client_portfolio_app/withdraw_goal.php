<?php
// withdraw_goal.php
// Handles withdrawing money from a financial goal.

session_start();
header('Content-Type: application/json');

include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['user_id'];

if (isset($_POST['goal_id'], $_POST['withdraw_amount'])) {
    $goalId = (int)$_POST['goal_id'];
    $withdrawAmount = (float)$_POST['withdraw_amount'];

    if ($withdrawAmount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Amount must be greater than zero.']);
        exit();
    }

    // Fetch the current goal details
    $stmt = $conn->prepare("SELECT saved_amount FROM financial_goals WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $goalId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $goal = $result->fetch_assoc();
    $stmt->close();

    if (!$goal) {
        echo json_encode(['status' => 'error', 'message' => 'Goal not found.']);
        exit();
    }

    if ($withdrawAmount > $goal['saved_amount']) {
        echo json_encode(['status' => 'error', 'message' => 'You cannot withdraw more than is saved in the goal.']);
        exit();
    }

    // Update the saved amount for the goal
    $newSavedAmount = $goal['saved_amount'] - $withdrawAmount;
    
    $stmt = $conn->prepare("UPDATE financial_goals SET saved_amount = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("dii", $newSavedAmount, $goalId, $userId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Money withdrawn from goal successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to withdraw money: ' . $conn->error]);
    }
    $stmt->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete data provided.']);
}

$conn->close();
