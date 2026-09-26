<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include('../config/connection.php');

/* =========================
   CREATE SALE
========================= */
if (isset($_POST['action']) && $_POST['action'] === 'create_sale') {

    $conn->begin_transaction();

    try {
        $items = json_decode($_POST['items'], true);
        $grand_total = 0;

        foreach ($items as $it) {
            $grand_total += $it['price'] * $it['quantity'];
        }

        // INSERT SALE
        $stmt = $conn->prepare("
            INSERT INTO sales 
            (invoice_no, customer_id, total)
            VALUES (?,?,?)
        ");
        $stmt->bind_param(
            "sid",
            $_POST['invoice_no'],
            $_POST['customer_id'],
            $grand_total
        );
        $stmt->execute();

        $sale_id = $stmt->insert_id;

        // INSERT SALE ITEMS
        foreach ($items as $item) {
            $stmt2 = $conn->prepare("
                INSERT INTO sale_items 
                (sale_id, medicine_id, quantity, price)
                VALUES (?,?,?,?)
            ");
            $stmt2->bind_param(
                "iiid",
                $sale_id,
                $item['medicine_id'],
                $item['quantity'],
                $item['price']
            );
            $stmt2->execute();

            // UPDATE STOCK
            $stmt3 = $conn->prepare("
                UPDATE medicines 
                SET quantity = quantity - ?
                WHERE id = ?
            ");
            $stmt3->bind_param(
                "ii",
                $item['quantity'],
                $item['medicine_id']
            );
            $stmt3->execute();
        }

        $conn->commit();
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        exit;
    }
}

/* =========================
   FETCH DATA
========================= */
$medicines = $conn->query("SELECT id, name, price, quantity FROM medicines WHERE quantity > 0");
$customers = $conn->query("SELECT id, name FROM customers");
$sales     = $conn->query("SELECT * FROM sales ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Sales | Pharmacy System</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="admin-theme">

<div class="d-flex">

<?php include('../includes/sidebar.php'); ?>

<div class="flex-grow-1">

<!-- TOP BAR -->
<div class="topbar d-flex align-items-center justify-content-between px-4">
    <h6 class="mb-0">Sales</h6>
    <button class="btn btn-sm"
        style="background-color: var(--primary-color); color:#fff"
        data-bs-toggle="modal" data-bs-target="#saleModal">
        + New Sale
    </button>
</div>

<!-- CONTENT -->
<div class="content">
<div class="card shadow-sm">
<div class="card-body">

<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
    <th>#</th>
    <th>Invoice</th>
    <th>Total</th>
    <th>Date</th>
</tr>
</thead>
<tbody>
<?php $i=1; while($row=$sales->fetch_assoc()): ?>
<tr>
    <td><?= $i++ ?></td>
    <td>
        <a href="invoice.php?id=<?= $row['id'] ?>" target="_blank">
            <?= $row['invoice_no'] ?>
        </a>
    </td>
    <td><?= number_format($row['total'],2) ?></td>
    <td><?= date('d-m-Y', strtotime($row['sale_date'])) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>
</div>
</div>

</div>
</div>

<!-- CREATE SALE MODAL -->
<div class="modal fade" id="saleModal" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
<h5>Create Sale</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="row mb-2">
    <div class="col-md-4">
        <input class="form-control" id="invoiceNo"
               value="INV-<?= time() ?>" readonly>
    </div>
    <div class="col-md-4">
        <select class="form-select" id="customer">
            <?php while($c=$customers->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>
</div>

<table class="table table-bordered">
<thead>
<tr>
    <th>Medicine</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Total</th>
    <th></th>
</tr>
</thead>
<tbody id="saleItems"></tbody>
</table>

<button class="btn btn-sm btn-outline-primary" onclick="addRow()">+ Add Item</button>

<h5 class="text-end mt-3">
    Grand Total: <span id="grandTotal">0</span>
</h5>

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn" style="background:var(--primary-color);color:#fff"
onclick="saveSale()">Save Sale</button>
</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let medicines = <?= json_encode($medicines->fetch_all(MYSQLI_ASSOC)) ?>;

function addRow() {
    let tr = document.createElement("tr");
    tr.innerHTML = `
        <td>
            <select class="form-select med">
                ${medicines.map(m=>`<option value="${m.id}" data-price="${m.price}">${m.name}</option>`).join("")}
            </select>
        </td>
        <td><input type="number" class="form-control qty" value="1" min="1"></td>
        <td class="price">0</td>
        <td class="total">0</td>
        <td><button class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();calc()">X</button></td>
    `;
    document.getElementById("saleItems").appendChild(tr);
    calc();
}

function calc(){
    let g=0;
    document.querySelectorAll("#saleItems tr").forEach(r=>{
        let price = parseFloat(r.querySelector(".med").selectedOptions[0].dataset.price);
        let qty = parseInt(r.querySelector(".qty").value);
        let total = price * qty;
        r.querySelector(".price").innerText = price.toFixed(2);
        r.querySelector(".total").innerText = total.toFixed(2);
        g += total;
    });
    document.getElementById("grandTotal").innerText = g.toFixed(2);
}

document.addEventListener("change", e=>{
    if(e.target.classList.contains("med") || e.target.classList.contains("qty")) calc();
});

function saveSale(){
    let items=[];
    document.querySelectorAll("#saleItems tr").forEach(r=>{
        items.push({
            medicine_id: r.querySelector(".med").value,
            quantity: parseInt(r.querySelector(".qty").value),
            price: parseFloat(r.querySelector(".price").innerText)
        });
    });

    let f=new FormData();
    f.append("action","create_sale");
    f.append("invoice_no",document.getElementById("invoiceNo").value);
    f.append("customer_id",document.getElementById("customer").value);
    f.append("items",JSON.stringify(items));

    fetch("",{method:"POST",body:f}).then(()=>location.reload());
}
</script>

<?php include("../includes/footer.php"); ?>
</body>
</html>
