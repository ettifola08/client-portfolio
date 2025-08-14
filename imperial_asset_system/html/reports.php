<?php
//
// reports.php
// Generates reports for the asset management system.
//
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imperial Asset Management - Reports</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <span class="logo-placeholder d-inline-block">IA</span>
                Imperial Assets
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clients.php">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="investments.php">Investments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reports.php">Reports</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text me-3 text-white">Welcome, <?php echo $_SESSION['username']; ?></span>
                    <a class="btn btn-danger btn-sm" href="../php/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content Container -->
    <div class="container mt-4">
        <h1 class="mb-4">Profit/Loss Report</h1>
        <div class="table-responsive">
            <table class="table table-hover bg-white rounded-3 shadow-sm">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Investment Type</th>
                        <th>Amount Invested</th>
                        <th>Current Value</th>
                        <th>Profit/Loss</th>
                        <th>ROI (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include '../php/db.php';
                    $sql = "SELECT i.investment_type, i.amount_invested, i.current_value, c.name AS client_name FROM investments i JOIN clients c ON i.client_id = c.client_id";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()):
                    $profitLoss = $row['current_value'] - $row['amount_invested'];
                    $roi = ($row['amount_invested'] > 0) ? ($profitLoss / $row['amount_invested']) * 100 : 0;
                    ?>
                    <tr>
                        <td><?php echo $row['client_name']; ?></td>
                        <td><?php echo $row['investment_type']; ?></td>
                        <td>$<?php echo number_format($row['amount_invested'], 2); ?></td>
                        <td>$<?php echo number_format($row['current_value'], 2); ?></td>
                        <td>$<?php echo number_format($profitLoss, 2); ?></td>
                        <td class="<?php echo ($roi >= 0) ? 'text-success' : 'text-danger'; ?>"><?php echo number_format($roi, 2); ?>%</td>
                    </tr>
                    <?php endwhile; $conn->close(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
