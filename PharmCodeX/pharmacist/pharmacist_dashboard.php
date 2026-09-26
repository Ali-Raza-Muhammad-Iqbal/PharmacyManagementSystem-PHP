<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "staff") {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pharmacist Dashboard | Pharmacy</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .topbar {
            background-color: #198754; /* Success color for pharmacist */
            color: #fff;
            padding: 15px 30px;
        }
        .card {
            border-radius: 12px;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .card h2 {
            font-weight: bold;
        }
        .card-icon {
            font-size: 30px;
            margin-bottom: 10px;
        }
        .sidebar {
            min-width: 220px;
            background-color: #343a40;
            color: #fff;
            min-height: 100vh;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
        }
        .sidebar a.active, .sidebar a:hover {
            color: #fff;
            background-color: #495057;
            border-radius: 8px;
        }
        .content {
            padding: 30px;
        }
    </style>
</head>
<body>

<div class="d-flex">
    
    <!-- Sidebar -->
    <?php include('../includes/sidebar.php'); ?>

    <!-- Main Content -->
    <div class="flex-grow-1">

        <!-- Topbar -->
        <div class="topbar d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Welcome, <?php echo $_SESSION['username'] ?? 'Pharmacist'; ?></h5>
            <span>Pharmacist Dashboard</span>
        </div>

        <!-- Page Content -->
        <div class="content">
            <div class="row g-4">

                <!-- Today Sales -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm p-3 bg-white">
                        <div class="card-icon text-success">
                            💰
                        </div>
                        <h5 class="card-title">Today Sales</h5>
                        <h2 class="text-success">₨ 12,500</h2>
                        <p class="text-muted">Compared to yesterday +5%</p>
                    </div>
                </div>

                <!-- Medicines Available -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm p-3 bg-white">
                        <div class="card-icon text-primary">
                            💊
                        </div>
                        <h5 class="card-title">Medicines Available</h5>
                        <h2 class="text-primary">95</h2>
                        <p class="text-muted">Total stock in inventory</p>
                    </div>
                </div>

                <!-- Customers -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm p-3 bg-white">
                        <div class="card-icon text-warning">
                            🧑‍🤝‍🧑
                        </div>
                        <h5 class="card-title">Customers</h5>
                        <h2 class="text-warning">18</h2>
                        <p class="text-muted">Registered today</p>
                    </div>
                </div>

            </div>

            <!-- Quick Actions -->
            <div class="row g-4 mt-4">
                <div class="col-md-6 col-lg-3">
                    <a href="../mutual/medicine.php" class="text-decoration-none">
                        <div class="card shadow-sm p-3 text-center bg-white">
                            <div class="card-icon text-info">➕</div>
                            <h6>Add Medicine</h6>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="../mutual/sales.php" class="text-decoration-none">
                        <div class="card shadow-sm p-3 text-center bg-white">
                            <div class="card-icon text-danger">📊</div>
                            <h6>View Sales</h6>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="../mutual/customers.php" class="text-decoration-none">
                        <div class="card shadow-sm p-3 text-center bg-white">
                            <div class="card-icon text-success">🧾</div>
                            <h6>Customer List</h6>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="../mutual/medicine.php" class="text-decoration-none">
                        <div class="card shadow-sm p-3 text-center bg-white">
                            <div class="card-icon text-warning">📦</div>
                            <h6>Inventory</h6>
                        </div>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
