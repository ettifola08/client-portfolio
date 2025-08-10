<?php
// delete_goal.php
// Handles the deletion of a financial goal from the database.

session_start();
header('Content-Type: application/json');

include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['user_id'];

if (isset($_POST['goal_id'])) {
    $goalId = (int)$_POST['goal_id'];

    // Delete the financial goal
    $stmt = $conn->prepare("DELETE FROM financial_goals WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $goalId, $userId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Goal deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Goal not found or you do not have permission to delete it.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete goal: ' . $conn->error]);
    }
    $stmt->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete data provided.']);
}

$conn->close();
