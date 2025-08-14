<?php
//
// clients.php
// Manages client data for the asset management system.
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
    <title>Imperial Asset Management - Clients</title>
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
                        <a class="nav-link active" href="clients.php">Clients</a>
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
        <h1 class="mb-4">Clients</h1>
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#clientModal">Add New Client</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover bg-white rounded-3 shadow-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include '../php/db.php';
                    $sql = "SELECT * FROM clients";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $row['client_id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['contact_email']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#clientModal"
                                data-client-id="<?php echo $row['client_id']; ?>"
                                data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                data-email="<?php echo htmlspecialchars($row['contact_email']); ?>"
                                data-phone="<?php echo htmlspecialchars($row['phone']); ?>"
                                data-address="<?php echo htmlspecialchars($row['address']); ?>">Edit</a>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                            <a href="../php/delete_client.php?id=<?php echo $row['client_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this client?');">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; $conn->close(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Client Modal (Add/Edit) -->
    <div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../php/add_client.php" method="POST" id="client-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clientModalLabel">Add Client</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="client-id" name="client_id">
                        <div class="mb-3">
                            <label for="client-name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="client-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="client-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="client-email" name="contact_email">
                        </div>
                        <div class="mb-3">
                            <label for="client-phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="client-phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="client-address" class="form-label">Address</label>
                            <textarea class="form-control" id="client-address" name="address" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const clientModal = document.getElementById('clientModal');
        clientModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const clientId = button.getAttribute('data-client-id');
            const form = document.getElementById('client-form');
            const modalTitle = document.getElementById('clientModalLabel');

            if (clientId) {
                modalTitle.textContent = 'Edit Client';
                form.action = '../php/edit_client.php';
                document.getElementById('client-id').value = clientId;
                document.getElementById('client-name').value = button.getAttribute('data-name');
                document.getElementById('client-email').value = button.getAttribute('data-email');
                document.getElementById('client-phone').value = button.getAttribute('data-phone');
                document.getElementById('client-address').value = button.getAttribute('data-address');
            } else {
                modalTitle.textContent = 'Add Client';
                form.action = '../php/add_client.php';
                form.reset();
            }
        });
    </script>
</body>
</html>
