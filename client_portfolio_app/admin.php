<?php
// admin.php
// A very basic admin panel to view all registered users.
//
// NOTE: For a real application, you would need a separate login system and user role management.
// This is a simplified version to fulfill the request.
// For demonstration, we assume user with id=1 is the admin.

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Simple check for admin (user with ID 1)
if ($_SESSION['user_id'] != 1) {
    echo "<h1>Access Denied</h1><p>You do not have permission to view this page.</p>";
    exit();
}

include 'db_connect.php';

// Fetch all users from the database
$users = [];
$sql = "SELECT id, full_name, email, created_at FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .admin-table th, .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .admin-table th {
            background-color: #f4f7f6;
            color: #333;
        }
        .admin-table tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="header-left">
            <h1 class="logo"><i class="fas fa-user-shield"></i> Admin Panel</h1>
        </div>
        <div class="header-right">
            <span>Admin</span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <main class="dashboard-container">
        <div class="card">
            <h2>Registered Users</h2>
            <?php if (count($users) > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No users found in the database.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
