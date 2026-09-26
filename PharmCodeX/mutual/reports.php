<?php
session_start();
include('../config/connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

/* =========================
   INPUTS
========================= */
$category = $_GET['category'] ?? '';
$from     = $_GET['from'] ?? '';
$to       = $_GET['to'] ?? '';
$export   = $_GET['export'] ?? '';

$data = null;
$totalQty = 0;
$totalAmount = 0;

/* =========================
   FETCH DATA
========================= */
if ($category && $from && $to) {

    if ($category === 'sales') {

        // SALES → sales + sale_items
        $sql = "
            SELECT 
                m.name,
                si.quantity,
                (si.quantity * si.price) AS total_amount,
                s.sale_date AS date_field
            FROM sale_items si
            JOIN sales s ON si.sale_id = s.id
            JOIN medicines m ON si.medicine_id = m.id
            WHERE DATE(s.sale_date) BETWEEN '$from' AND '$to'
            ORDER BY s.sale_date DESC
        ";

    } else {

        // PURCHASES
        $sql = "
            SELECT 
                m.name,
                p.quantity,
                p.total_amount,
                p.purchase_date AS date_field
            FROM purchases p
            JOIN medicines m ON p.medicine_id = m.id
            WHERE DATE(p.purchase_date) BETWEEN '$from' AND '$to'
            ORDER BY p.purchase_date DESC
        ";
    }

    $data = $conn->query($sql);
}

/* =========================
   EXPORT HANDLER
========================= */
if ($export && $data) {

    ob_start();
    ?>

    <h3 style="text-align:center"><?= strtoupper($category) ?> REPORT</h3>
    <p style="text-align:center">From <?= $from ?> To <?= $to ?></p>

    <table border="1" width="100%" cellpadding="8" cellspacing="0">
        <thead>
        <tr>
            <th>#</th>
            <th>Medicine</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        <?php $i=1; while($row=$data->fetch_assoc()): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= number_format($row['total_amount'],2) ?></td>
                <td><?= date("d-M-Y", strtotime($row['date_field'])) ?></td>
            </tr>
        <?php
            $totalQty += $row['quantity'];
            $totalAmount += $row['total_amount'];
        endwhile; ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="2">TOTAL</th>
            <th><?= $totalQty ?></th>
            <th><?= number_format($totalAmount,2) ?></th>
            <th></th>
        </tr>
        </tfoot>
    </table>

    <?php
    $html = ob_get_clean();
    $filename = $category.'_report_'.date('Ymd_His');

    if ($export === 'excel') {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename.xls");
        echo $html; exit;
    }

    if ($export === 'word') {
        header("Content-Type: application/msword");
        header("Content-Disposition: attachment; filename=$filename.doc");
        echo $html; exit;
    }

    if ($export === 'pdf') {
        require_once('../libs/dompdf/autoload.inc.php');
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','landscape');
        $dompdf->render();
        $dompdf->stream($filename);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports | Pharmacy</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{background:#f8f9fa}
.topbar{background:#fff;border-bottom:1px solid #ddd;height:60px}
</style>
</head>

<body>

<div class="d-flex">

<?php include('../includes/sidebar.php'); ?>

<div class="flex-grow-1">

<div class="topbar d-flex align-items-center px-4">
    <h6 class="mb-0">Reports</h6>
</div>

<div class="p-4">

<!-- FILTER -->
<div class="card shadow-sm mb-4">
<div class="card-body">
<form method="GET" class="row g-3 align-items-end">

    <div class="col-md-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-select" required>
            <option value="sales" <?= $category=='sales'?'selected':'' ?>>Sales</option>
            <option value="purchase" <?= $category=='purchase'?'selected':'' ?>>Purchase</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">From</label>
        <input type="date" name="from" value="<?= $from ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">To</label>
        <input type="date" name="to" value="<?= $to ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <button class="btn btn-primary w-100">
            <i class="bi bi-bar-chart"></i> Generate
        </button>
    </div>

</form>
</div>
</div>

<?php if ($data): ?>
<!-- REPORT -->
<div class="card shadow-sm">
<div class="card-body">

<div class="d-flex justify-content-between mb-3">
    <h6><?= strtoupper($category) ?> REPORT</h6>
    <div>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer"></i> Print
        </button>

        <a href="?<?= http_build_query(array_merge($_GET,['export'=>'excel'])) ?>" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </a>

        <a href="?<?= http_build_query(array_merge($_GET,['export'=>'pdf'])) ?>" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf"></i> PDF
        </a>

        <a href="?<?= http_build_query(array_merge($_GET,['export'=>'word'])) ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-file-earmark-word"></i> Word
        </a>
    </div>
</div>

<table class="table table-bordered table-hover">
<thead class="table-light">
<tr>
<th>#</th>
<th>Medicine</th>
<th>Quantity</th>
<th>Total</th>
<th>Date</th>
</tr>
</thead>
<tbody>
<?php $i=1; while($row=$data->fetch_assoc()): ?>
<tr>
<td><?= $i++ ?></td>
<td><?= $row['name'] ?></td>
<td><?= $row['quantity'] ?></td>
<td><?= number_format($row['total_amount'],2) ?></td>
<td><?= date("d-M-Y", strtotime($row['date_field'])) ?></td>
</tr>
<?php
$totalQty += $row['quantity'];
$totalAmount += $row['total_amount'];
endwhile; ?>
</tbody>
<tfoot class="table-dark">
<tr>
<th colspan="2">TOTAL</th>
<th><?= $totalQty ?></th>
<th><?= number_format($totalAmount,2) ?></th>
<th></th>
</tr>
</tfoot>
</table>

</div>
</div>
<?php endif; ?>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
