<?php
// delete_asset.php
// Handles the deletion of an asset from a user's portfolio.

session_start();
header('Content-Type: application/json');

include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['user_id'];

if (isset($_POST['asset_name'])) {
    $assetName = trim($_POST['asset_name']);

    // Check if the asset exists and belongs to the user
    $stmt = $conn->prepare("SELECT id FROM portfolios WHERE user_id = ? AND asset_name = ?");
    $stmt->bind_param("is", $userId, $assetName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Asset not found or you do not have permission to delete it.']);
        $stmt->close();
        exit();
    }
    $stmt->close();
    
    // Delete the asset from the portfolio
    $stmt = $conn->prepare("DELETE FROM portfolios WHERE user_id = ? AND asset_name = ?");
    $stmt->bind_param("is", $userId, $assetName);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Asset deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete asset.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete asset: ' . $conn->error]);
    }
    $stmt->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete data provided.']);
}

$conn->close();
