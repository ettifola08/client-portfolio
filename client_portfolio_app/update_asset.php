<?php
// update_asset.php
// Handles both buy and sell transactions, updating the user's portfolio.
// This version includes a bug fix for the "asset not found" error during sell orders.

session_start();
header('Content-Type: application/json');

include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['user_id'];

// Check if all required fields are set
if (isset($_POST['asset_name'], $_POST['quantity'], $_POST['price'], $_POST['action'])) {
    $assetName = trim($_POST['asset_name']); // Use trim to handle any leading/trailing whitespace
    $quantity = (float)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $action = $_POST['action'];

    if ($action === 'buy') {
        // Handle a 'buy' transaction
        // First, check if the asset already exists in the portfolio
        $stmt = $conn->prepare("SELECT quantity, purchase_price FROM portfolios WHERE user_id = ? AND asset_name = ?");
        if ($stmt === false) {
            echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param("is", $userId, $assetName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Asset exists, so update the quantity and calculate new average purchase price
            $existingQuantity = (float)$row['quantity'];
            $existingPrice = (float)$row['purchase_price'];

            $newQuantity = $existingQuantity + $quantity;
            $newPurchasePrice = (($existingQuantity * $existingPrice) + ($quantity * $price)) / $newQuantity;
            
            $stmt->close();
            $stmt = $conn->prepare("UPDATE portfolios SET quantity = ?, purchase_price = ?, current_price = ? WHERE user_id = ? AND asset_name = ?");
            if ($stmt === false) {
                echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
                exit();
            }
            $stmt->bind_param("dddis", $newQuantity, $newPurchasePrice, $price, $userId, $assetName);
        } else {
            // Asset does not exist, so insert a new row
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO portfolios (user_id, asset_name, quantity, purchase_price, current_price) VALUES (?, ?, ?, ?, ?)");
            if ($stmt === false) {
                echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
                exit();
            }
            $stmt->bind_param("isddd", $userId, $assetName, $quantity, $price, $price);
        }
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Asset purchased successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to execute buy order: ' . $stmt->error]);
        }

    } elseif ($action === 'sell') {
        // Handle a 'sell' transaction
        $stmt = $conn->prepare("SELECT quantity FROM portfolios WHERE user_id = ? AND asset_name = ?");
        if ($stmt === false) {
            echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param("is", $userId, $assetName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $existingQuantity = (float)$row['quantity'];
            if ($quantity > $existingQuantity) {
                echo json_encode(['status' => 'error', 'message' => 'You cannot sell more than you own.']);
                exit();
            }

            $newQuantity = $existingQuantity - $quantity;
            
            $stmt->close();
            if ($newQuantity > 0) {
                // Update the quantity and current price
                $stmt = $conn->prepare("UPDATE portfolios SET quantity = ?, current_price = ? WHERE user_id = ? AND asset_name = ?");
                if ($stmt === false) {
                    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
                    exit();
                }
                $stmt->bind_param("ddis", $newQuantity, $price, $userId, $assetName);
            } else {
                // Quantity is zero, so delete the asset from the portfolio
                $stmt = $conn->prepare("DELETE FROM portfolios WHERE user_id = ? AND asset_name = ?");
                if ($stmt === false) {
                    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
                    exit();
                }
                $stmt->bind_param("is", $userId, $assetName);
            }

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Asset sold successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to execute sell order: ' . $stmt->error]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Asset not found in your portfolio.']);
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
    }

    if ($stmt) $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete data provided.']);
}

$conn->close();
