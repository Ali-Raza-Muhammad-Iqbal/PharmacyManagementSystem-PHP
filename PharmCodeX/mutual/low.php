<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include('../config/connection.php');

/* =========================
   FETCH LOW STOCK MEDICINES
   Quantity < 50
========================= */
$lowStock = $conn->query(
    "SELECT id, name, type, batch_no, quantity, location, expiry_date
     FROM medicines
     WHERE quantity < 50
     ORDER BY quantity ASC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Low Stock Medicines</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="d-flex">

    <!-- Sidebar -->
    <?php include('../includes/sidebar.php'); ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Low Stock Medicines
            </h4>
            <span class="badge bg-danger fs-6">
                Threshold: Less than 50
            </span>
        </div>

        <!-- Table -->
        <div class="card shadow-sm">
            <div class="card-body">

                <?php if ($lowStock->num_rows > 0) { ?>

                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Medicine</th>
                            <th>Type</th>
                            <th>Batch No</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while($row = $lowStock->fetch_assoc()) { ?>
                        <tr class="<?= ($row['quantity'] < 20) ? 'table-danger' : 'table-warning' ?>">
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['type']) ?></td>
                            <td><?= htmlspecialchars($row['batch_no']) ?></td>
                            <td class="fw-bold"><?= $row['quantity'] ?></td>
                            <td><?= htmlspecialchars($row['location']) ?></td>
                            <td><?= date("d-M-Y", strtotime($row['expiry_date'])) ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <?php } else { ?>

                <div class="alert alert-success text-center mb-0">
                    <i class="bi bi-check-circle-fill"></i>
                    All medicines are sufficiently stocked 🎉
                </div>

                <?php } ?>

            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
