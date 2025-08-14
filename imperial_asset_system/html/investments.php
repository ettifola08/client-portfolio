<?php
//
// investments.php
// Manages investment data for the asset management system.
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
    <title>Imperial Asset Management - Investments</title>
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
                        <a class="nav-link active" href="investments.php">Investments</a>
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
        <h1 class="mb-4">Investments</h1>
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#investmentModal">Add New Investment</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover bg-white rounded-3 shadow-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Amount Invested</th>
                        <th>Current Value</th>
                        <th>Profit/Loss</th>
                        <th>Date Invested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include '../php/db.php';
                    $sql = "SELECT i.*, c.name AS client_name FROM investments i JOIN clients c ON i.client_id = c.client_id";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()):
                    $profitLoss = $row['current_value'] - $row['amount_invested'];
                    ?>
                    <tr>
                        <td><?php echo $row['investment_id']; ?></td>
                        <td><?php echo $row['client_name']; ?></td>
                        <td><?php echo $row['investment_type']; ?></td>
                        <td>$<?php echo number_format($row['amount_invested'], 2); ?></td>
                        <td>$<?php echo number_format($row['current_value'], 2); ?></td>
                        <td class="<?php echo ($profitLoss >= 0) ? 'text-success' : 'text-danger'; ?>">
                            $<?php echo number_format($profitLoss, 2); ?>
                        </td>
                        <td><?php echo $row['date_invested']; ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#investmentModal"
                                data-investment-id="<?php echo $row['investment_id']; ?>"
                                data-client-id="<?php echo $row['client_id']; ?>"
                                data-investment-type="<?php echo htmlspecialchars($row['investment_type']); ?>"
                                data-amount-invested="<?php echo $row['amount_invested']; ?>"
                                data-current-value="<?php echo $row['current_value']; ?>">Edit</a>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                            <a href="../php/delete_investment.php?id=<?php echo $row['investment_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this investment?');">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; $conn->close(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Investment Modal (Add/Edit) -->
    <div class="modal fade" id="investmentModal" tabindex="-1" aria-labelledby="investmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../php/add_investment.php" method="POST" id="investment-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="investmentModalLabel">Add Investment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="investment-id" name="investment_id">
                        <div class="mb-3">
                            <label for="investment-client" class="form-label">Client</label>
                            <select class="form-select" id="investment-client" name="client_id" required>
                                <?php
                                include '../php/db.php';
                                $result_clients = $conn->query("SELECT client_id, name FROM clients");
                                while ($client_row = $result_clients->fetch_assoc()):
                                ?>
                                <option value="<?php echo $client_row['client_id']; ?>"><?php echo $client_row['name']; ?></option>
                                <?php endwhile; $conn->close(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="investment-type" class="form-label">Investment Type</label>
                            <input type="text" class="form-control" id="investment-type" name="investment_type" required>
                        </div>
                        <div class="mb-3">
                            <label for="investment-amount" class="form-label">Amount Invested</label>
                            <input type="number" class="form-control" id="investment-amount" name="amount_invested" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="investment-current-value" class="form-label">Current Value</label>
                            <input type="number" class="form-control" id="investment-current-value" name="current_value" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Investment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const investmentModal = document.getElementById('investmentModal');
        investmentModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const investmentId = button.getAttribute('data-investment-id');
            const form = document.getElementById('investment-form');
            const modalTitle = document.getElementById('investmentModalLabel');

            if (investmentId) {
                modalTitle.textContent = 'Edit Investment';
                form.action = '../php/edit_investment.php';
                document.getElementById('investment-id').value = investmentId;
                document.getElementById('investment-client').value = button.getAttribute('data-client-id');
                document.getElementById('investment-type').value = button.getAttribute('data-investment-type');
                document.getElementById('investment-amount').value = button.getAttribute('data-amount-invested');
                document.getElementById('investment-current-value').value = button.getAttribute('data-current-value');
            } else {
                modalTitle.textContent = 'Add Investment';
                form.action = '../php/add_investment.php';
                form.reset();
            }
        });
    </script>
</body>
</html>