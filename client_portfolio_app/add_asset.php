<?php
// add_asset.php
// Handles the addition of a new asset to a client's portfolio.
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
    $assetName = trim($_POST['asset_name']);
    $quantity = floatval($_POST['quantity']);
    $purchasePrice = floatval($_POST['purchase_price']);

    // Check for existing asset to prevent duplicates for the same user
    $stmt = $conn->prepare("SELECT id, quantity, purchase_price FROM portfolios WHERE user_id = ? AND asset_name = ?");
    $stmt->bind_param("is", $userId, $assetName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Asset exists, update the existing entry (this is a simplified buy logic)
        $row = $result->fetch_assoc();
        $existingId = $row['id'];
        $existingQuantity = $row['quantity'];
        $existingPurchasePrice = $row['purchase_price'];
        
        $newQuantity = $existingQuantity + $quantity;
        // Simplified average price calculation
        $newPurchasePrice = (($existingQuantity * $existingPurchasePrice) + ($quantity * $purchasePrice)) / $newQuantity;

        $stmt = $conn->prepare("UPDATE portfolios SET quantity = ?, purchase_price = ?, current_price = ? WHERE id = ?");
        $stmt->bind_param("dddi", $newQuantity, $newPurchasePrice, $purchasePrice, $existingId);
        $stmt->execute();

        // Also log the transaction
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, transaction_type, asset_name, quantity, price_per_unit) VALUES (?, 'buy', ?, ?, ?)");
        $stmt->bind_param("isdd", $userId, $assetName, $quantity, $purchasePrice);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Asset updated successfully!', 'updated' => true]);

    } else {
        // Asset does not exist, insert a new one
        $stmt = $conn->prepare("INSERT INTO portfolios (user_id, asset_name, quantity, purchase_price, current_price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isddd", $userId, $assetName, $quantity, $purchasePrice, $purchasePrice);
        if ($stmt->execute()) {
            // Also log the transaction
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, transaction_type, asset_name, quantity, price_per_unit) VALUES (?, 'buy', ?, ?, ?)");
            $stmt->bind_param("isdd", $userId, $assetName, $quantity, $purchasePrice);
            $stmt->execute();
            echo json_encode(['status' => 'success', 'message' => 'New asset added to your portfolio!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add asset. Please try again.']);
        }
    }
    
    $stmt->close();
    $conn->close();
}
?>
