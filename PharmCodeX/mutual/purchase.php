<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include('../config/connection.php');

/* =========================
   ADD PURCHASE
========================= */
if (isset($_POST['add_purchase'])) {

    $medicine_id = $_POST['medicine_id'];
    $supplier    = $_POST['supplier']; // company name from suppliers table
    $quantity    = $_POST['quantity'];
    $price       = $_POST['price'];
    $total       = $quantity * $price;
    $date        = $_POST['purchase_date'];

    // Insert Purchase
    $stmt = $conn->prepare(
        "INSERT INTO purchases 
        (medicine_id, supplier, quantity, purchase_price, total_amount, purchase_date, added_by)
        VALUES (?,?,?,?,?,?,?)"
    );
    $stmt->bind_param(
        "isiddsi",
        $medicine_id,
        $supplier,
        $quantity,
        $price,
        $total,
        $date,
        $_SESSION['user_id']
    );
    $stmt->execute();

    // Update medicine stock
    $update = $conn->prepare(
        "UPDATE medicines SET quantity = quantity + ? WHERE id = ?"
    );
    $update->bind_param("ii", $quantity, $medicine_id);
    $update->execute();

    header("Location: purchase.php?success=1");
    exit();
}

/* =========================
   FETCH DATA
========================= */
$medicines = $conn->query(
    "SELECT id, name FROM medicines ORDER BY name ASC"
);

$suppliers = $conn->query(
    "SELECT company FROM suppliers ORDER BY company ASC"
);

$purchases = $conn->query(
    "SELECT p.*, m.name AS medicine_name
     FROM purchases p
     JOIN medicines m ON p.medicine_id = m.id
     ORDER BY p.id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Purchase Management</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="d-flex">
    <?php include('../includes/sidebar.php'); ?>

    <div class="flex-grow-1 p-4">

        <!-- TOP BAR -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Purchase Management</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">
                <i class="bi bi-plus-circle"></i> Add Purchase
            </button>
        </div>

        <!-- ALERT -->
        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success">Purchase added successfully!</div>
        <?php } ?>

        <!-- PURCHASE TABLE -->
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Medicine</th>
                            <th>Supplier</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; while($row = $purchases->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['medicine_name']) ?></td>
                            <td><?= htmlspecialchars($row['supplier']) ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td><?= number_format($row['purchase_price'],2) ?></td>
                            <td><?= number_format($row['total_amount'],2) ?></td>
                            <td><?= date("d-M-Y", strtotime($row['purchase_date'])) ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- ADD PURCHASE MODAL -->
<div class="modal fade" id="addPurchaseModal">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5>Add Purchase</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">

                <div class="col-md-6">
                    <label class="form-label">Medicine</label>
                    <select name="medicine_id" class="form-select" required>
                        <option value="">Select Medicine</option>
                        <?php while($m = $medicines->fetch_assoc()) { ?>
                            <option value="<?= $m['id'] ?>">
                                <?= htmlspecialchars($m['name']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <select name="supplier" class="form-select" required>
                        <option value="">Select Supplier</option>
                        <?php while($s = $suppliers->fetch_assoc()) { ?>
                            <option value="<?= htmlspecialchars($s['company']) ?>">
                                <?= htmlspecialchars($s['company']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Purchase Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" name="purchase_date" class="form-control" required>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_purchase" class="btn btn-primary">
                    Save Purchase
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
