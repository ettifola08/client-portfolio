<?php
// fund_goal.php
// Handles funding a financial goal from the user's total portfolio value.

session_start();
header('Content-Type: application/json');

include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['user_id'];

if (isset($_POST['goal_id'], $_POST['fund_amount'])) {
    $goalId = (int)$_POST['goal_id'];
    $fundAmount = (float)$_POST['fund_amount'];

    if ($fundAmount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Amount must be greater than zero.']);
        exit();
    }

    // Fetch the current goal details and user's cash balance
    $stmt = $conn->prepare("SELECT saved_amount, target_amount FROM financial_goals WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $goalId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $goal = $result->fetch_assoc();
    $stmt->close();

    if (!$goal) {
        echo json_encode(['status' => 'error', 'message' => 'Goal not found.']);
        exit();
    }

    // Update the saved amount for the goal
    $newSavedAmount = $goal['saved_amount'] + $fundAmount;
    
    // Ensure we don't exceed the target amount
    if ($newSavedAmount > $goal['target_amount']) {
        echo json_encode(['status' => 'error', 'message' => 'The amount you are trying to fund exceeds the target amount.']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE financial_goals SET saved_amount = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("dii", $newSavedAmount, $goalId, $userId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Goal funded successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fund goal: ' . $conn->error]);
    }
    $stmt->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete data provided.']);
}

$conn->close();
