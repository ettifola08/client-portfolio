<?php
// login.php
// Handles client login for TrustBanc.
//
// This script verifies the client's email and hashed password. If successful, it starts a session and
// redirects them to the main dashboard.

session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $fullName, $hashedPassword);
        $stmt->fetch();

        // Verify the password against the stored hash
        if (password_verify($password, $hashedPassword)) {
            // Password is correct, start a session
            $_SESSION['user_id'] = $userId;
            $_SESSION['full_name'] = $fullName;
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['message'] = "Invalid email or password. Please try again.";
            $_SESSION['status'] = "error";
            header("Location: index.html?section=login&message=" . urlencode($_SESSION['message']) . "&status=" . $_SESSION['status']);
            exit();
        }
    } else {
        $_SESSION['message'] = "Invalid email or password. Please try again.";
        $_SESSION['status'] = "error";
        header("Location: index.html?section=login&message=" . urlencode($_SESSION['message']) . "&status=" . $_SESSION['status']);
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
