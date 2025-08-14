<?php
//
// login.php
// Handles user authentication
//
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE username = ? AND password = ? AND role = ?");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: ../html/dashboard.html");
    } else {
        // Simple error handling, can be improved
        header("Location: ../html/login.html?error=1");
    }

    $stmt->close();
    $conn->close();
}
?>
