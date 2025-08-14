<?php
//
// dashboard.php
// Main dashboard for the asset management system.
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
    <title>Imperial Asset Management - Dashboard</title>
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
                        <a class="nav-link active" aria-current="page" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clients.php">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="investments.php">Investments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">Reports</a>
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
        <h1 class="mb-4">Dashboard</h1>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card bg-primary text-white shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title text-uppercase fw-bold">Total Clients</h5>
                                <h1 class="display-4 fw-bold">
                                    <?php
                                    include '../php/db.php';
                                    $result = $conn->query("SELECT COUNT(*) AS total FROM clients");
                                    $row = $result->fetch_assoc();
                                    echo $row['total'];
                                    $conn->close();
                                    ?>
                                </h1>
                            </div>
                            <i class="bi bi-people-fill display-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card bg-success text-white shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title text-uppercase fw-bold">Total Investments</h5>
                                <h1 class="display-4 fw-bold">
                                    <?php
                                    include '../php/db.php';
                                    $result = $conn->query("SELECT COUNT(*) AS total FROM investments");
                                    $row = $result->fetch_assoc();
                                    echo $row['total'];
                                    $conn->close();
                                    ?>
                                </h1>
                            </div>
                            <i class="bi bi-graph-up display-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card bg-info text-white shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title text-uppercase fw-bold">Total Assets</h5>
                                <h1 class="display-4 fw-bold">
                                    $<?php
                                    include '../php/db.php';
                                    $result = $conn->query("SELECT SUM(current_value) AS total_assets FROM investments");
                                    $row = $result->fetch_assoc();
                                    echo number_format($row['total_assets'], 2);
                                    $conn->close();
                                    ?>
                                </h1>
                            </div>
                            <i class="bi bi-currency-dollar display-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h2 class="mb-3">Recent Activity</h2>
            <div class="table-responsive">
                <table class="table table-hover bg-white rounded-3 shadow-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Placeholder for recent activity -->
                        <tr>
                            <td>2023-10-27</td>
                            <td>Added new client: John Doe</td>
                        </tr>
                        <tr>
                            <td>2023-10-26</td>
                            <td>Updated investment for Jane Smith</td>
                        </tr>
                        <tr>
                            <td>2023-10-25</td>
                            <td>Generated quarterly report</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>
