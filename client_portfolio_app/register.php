<?php
// register.php
// Handles new client registration for TrustBanc.
//
// This script checks if the email already exists and securely hashes the password before saving it.

session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "This email is already registered. Please log in or use a different email.";
        $_SESSION['status'] = "error";
        header("Location: index.html?section=register&message=" . urlencode($_SESSION['message']) . "&status=" . $_SESSION['status']);
        exit();
    }

    $stmt->close();

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user into the database with a default risk profile
    $defaultRiskProfile = 'Conservative';
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, risk_profile) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullName, $email, $hashedPassword, $defaultRiskProfile);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful! You can now log in to your dashboard.";
        $_SESSION['status'] = "success";
        header("Location: index.html?section=login&message=" . urlencode($_SESSION['message']) . "&status=" . $_SESSION['status']);
        exit();
    } else {
        $_SESSION['message'] = "Error during registration. Please try again.";
        $_SESSION['status'] = "error";
        header("Location: index.html?section=register&message=" . urlencode($_SESSION['message']) . "&status=" . $_SESSION['status']);
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
