<?php
include('../config/connection.php');

$sale_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* =========================
   FETCH SALE + CUSTOMER
========================= */
$saleStmt = $conn->prepare("
    SELECT 
        s.invoice_no,
        s.subtotal,
        s.discount,
        s.tax,
        s.total,
        s.sale_date,
        c.name AS customer_name
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    WHERE s.id = ?
");
$saleStmt->bind_param("i", $sale_id);
$saleStmt->execute();
$sale = $saleStmt->get_result()->fetch_assoc();

/* =========================
   FETCH SALE ITEMS
========================= */
$itemStmt = $conn->prepare("
    SELECT 
        si.quantity,
        si.price,
        m.name AS medicine_name
    FROM sale_items si
    INNER JOIN medicines m ON si.medicine_id = m.id
    WHERE si.sale_id = ?
");
$itemStmt->bind_param("i", $sale_id);
$itemStmt->execute();
$items = $itemStmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Invoice</title>

<style>
body{
    font-family: Arial, Helvetica, sans-serif;
    background:#f5f5f5;
}
.invoice-box{
    width:800px;
    margin:20px auto;
    background:#fff;
    padding:30px;
    border:1px solid #ddd;
}
.header{
    text-align:center;
    border-bottom:2px solid #000;
    padding-bottom:10px;
}
.header h2{
    margin:0;
    font-size:26px;
}
.header p{
    margin:3px 0;
    font-size:14px;
}
.info{
    margin-top:20px;
    display:flex;
    justify-content:space-between;
}
.info div{
    font-size:14px;
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}
table th, table td{
    border:1px solid #000;
    padding:8px;
    text-align:center;
    font-size:14px;
}
table th{
    background:#f0f0f0;
}
.summary{
    width:300px;
    margin-left:auto;
    margin-top:15px;
    font-size:14px;
}
.summary div{
    display:flex;
    justify-content:space-between;
    margin-bottom:5px;
}
.summary .grand{
    font-weight:bold;
    font-size:16px;
    border-top:1px solid #000;
    padding-top:5px;
}
.footer{
    margin-top:40px;
    border-top:1px dashed #000;
    padding-top:10px;
    font-size:13px;
    text-align:center;
}
.print-btn{
    text-align:center;
    margin-top:15px;
}
@media print{
    body{background:#fff;}
    .print-btn{display:none;}
}
</style>
</head>

<body>

<div class="invoice-box">

    <!-- HEADER -->
    <div class="header">
        <h2>Manzoor Medical Store</h2>
        <p>XYZ, Pakistan</p>
        <p>Phone: 0300000000</p>
    </div>

    <!-- INFO -->
    <div class="info">
        <div>
            <strong>Invoice No:</strong> <?= htmlspecialchars($sale['invoice_no']) ?><br>
            <strong>Date:</strong> <?= date('d-m-Y', strtotime($sale['sale_date'])) ?>
        </div>
        <div>
            <strong>Customer:</strong>
            <?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer') ?>
        </div>
    </div>

    <!-- ITEMS -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Medicine</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php $i=1; while($row=$items->fetch_assoc()): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['medicine_name']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= number_format($row['price'],2) ?></td>
                <td><?= number_format($row['price'] * $row['quantity'],2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- SUMMARY -->
    <div class="summary">
        <div>
            <span>Subtotal:</span>
            <span>PKR <?= number_format($sale['subtotal'],2) ?></span>
        </div>
        <div>
            <span>Discount:</span>
            <span>- PKR <?= number_format($sale['discount'],2) ?></span>
        </div>
        <div>
            <span>Tax:</span>
            <span>PKR <?= number_format($sale['tax'],2) ?></span>
        </div>
        <div class="grand">
            <span>Grand Total:</span>
            <span>PKR <?= number_format($sale['total'],2) ?></span>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p><strong>Software Developer:</strong> Ali Raza</p>
        <p><strong>Phone (WhatsApp):</strong> +92 307 6871720</p>
        <p>Please contact us to get your business software</p>
    </div>

    <!-- PRINT -->
    <div class="print-btn">
        <button onclick="window.print()">🖨 Print Invoice</button>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
