<?php
// include('../auth/session.php');
include('../config/connection.php');

/* =========================
   ADD / UPDATE / DELETE
========================= */
if (isset($_POST['action'])) {

    // ADD MEDICINE
    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare(
            "INSERT INTO medicines 
            (name, type, batch_no, price, quantity, location, expiry_date)
            VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->bind_param(
            "sssdiss",
            $_POST['name'],
            $_POST['type'],
            $_POST['batch'],
            $_POST['price'],
            $_POST['quantity'],
            $_POST['location'],
            $_POST['expiry']
        );
        $stmt->execute();
        exit;
    }

    // UPDATE MEDICINE
    if ($_POST['action'] === 'update') {
        $stmt = $conn->prepare(
            "UPDATE medicines SET 
                name=?, type=?, batch_no=?, price=?, quantity=?, location=?, expiry_date=?
             WHERE id=?"
        );
        $stmt->bind_param(
            "sssdissi",
            $_POST['name'],
            $_POST['type'],
            $_POST['batch'],
            $_POST['price'],
            $_POST['quantity'],
            $_POST['location'],
            $_POST['expiry'],
            $_POST['id']
        );
        $stmt->execute();
        exit;
    }

    // DELETE MEDICINE
    if ($_POST['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM medicines WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        exit;
    }
}

/* =========================
   FETCH MEDICINES
========================= */
$result = $conn->query("SELECT * FROM medicines");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Medicines | Pharmacy System</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="admin-theme">

<div class="d-flex">

<?php include('../includes/sidebar.php'); ?>

<div class="flex-grow-1">

<div class="topbar d-flex align-items-center justify-content-between px-4">
    <h6 class="mb-0">Medicines</h6>
    <button class="btn btn-sm"
        style="background-color: var(--primary-color); color:#fff"
        data-bs-toggle="modal" data-bs-target="#addMedicineModal">
        + Add Medicine
    </button>
</div>

<div class="content">
<div class="card shadow-sm">
<div class="card-body">

<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
    <th>#</th>
    <th>Name</th>
    <th>Type</th>
    <th>Batch</th>
    <th>Price</th>
    <th>Stock</th>
    <th>Location</th>
    <th>Expiry</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php $i=1; while($row=$result->fetch_assoc()): ?>
<tr data-id="<?= $row['id'] ?>">
    <td><?= $i++ ?></td>
    <td class="name"><?= htmlspecialchars($row['name']) ?></td>
    <td class="type"><?= htmlspecialchars($row['type']) ?></td>
    <td class="batch"><?= htmlspecialchars($row['batch_no']) ?></td>
    <td class="price"><?= $row['price'] ?></td>
    <td class="qty"><?= $row['quantity'] ?></td>
    <td class="location"><?= htmlspecialchars($row['location']) ?></td>
    <td class="expiry"><?= $row['expiry_date'] ?></td>
    <td>
        <button class="btn btn-sm btn-outline-primary" onclick="openEditModal(this)">Edit</button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteMedicine(this)">Delete</button>
    </td>
</tr>
<?php endwhile; ?>
</tbody>

</table>

</div>
</div>
</div>

</div>
</div>

<!-- ADD MEDICINE MODAL -->
<div class="modal fade" id="addMedicineModal" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
<h5>Add Medicine</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="row">
<input class="form-control mb-2" id="newName" placeholder="Name">
<select class="form-select mb-2" id="newType">
<option>Tablet</option><option>Capsule</option><option>Syrup</option>
<option>Injection</option><option>Cream</option><option>Drops</option><option>Others</option>
</select>
<input class="form-control mb-2" id="newBatch" placeholder="Batch">
<input class="form-control mb-2" type="number" id="newPrice" placeholder="Price">
<input class="form-control mb-2" type="number" id="newQty" placeholder="Quantity">
<input class="form-control mb-2" id="newLocation" placeholder="Location">
<input class="form-control mb-2" type="date" id="newExpiry">
</div>
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn" style="background:var(--primary-color);color:#fff"
onclick="addMedicine()">Save</button>
</div>
</div>
</div>
</div>

<!-- EDIT MEDICINE MODAL -->
<div class="modal fade" id="editMedicineModal" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
<h5>Edit Medicine</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input class="form-control mb-2" id="editName">
<select class="form-select mb-2" id="editType">
<option>Tablet</option><option>Capsule</option><option>Syrup</option>
<option>Injection</option><option>Cream</option><option>Drops</option><option>Others</option>
</select>
<input class="form-control mb-2" id="editBatch">
<input class="form-control mb-2" type="number" id="editPrice">
<input class="form-control mb-2" type="number" id="editQty">
<input class="form-control mb-2" id="editLocation">
<input class="form-control mb-2" type="date" id="editExpiry">
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn" style="background:var(--primary-color);color:#fff"
onclick="updateMedicine()">Update</button>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let editRow;

function addMedicine(){
    let f=new FormData();
    f.append("action","add");
    f.append("name",newName.value);
    f.append("type",newType.value);
    f.append("batch",newBatch.value);
    f.append("price",newPrice.value);
    f.append("quantity",newQty.value);
    f.append("location",newLocation.value);
    f.append("expiry",newExpiry.value);
    fetch("",{method:"POST",body:f}).then(()=>location.reload());
}

function openEditModal(btn){
    editRow=btn.closest("tr");
    editName.value=editRow.querySelector(".name").innerText;
    editType.value=editRow.querySelector(".type").innerText;
    editBatch.value=editRow.querySelector(".batch").innerText;
    editPrice.value=editRow.querySelector(".price").innerText;
    editQty.value=editRow.querySelector(".qty").innerText;
    editLocation.value=editRow.querySelector(".location").innerText;
    editExpiry.value=editRow.querySelector(".expiry").innerText;
    new bootstrap.Modal(editMedicineModal).show();
}

function updateMedicine(){
    let f=new FormData();
    f.append("action","update");
    f.append("id",editRow.dataset.id);
    f.append("name",editName.value);
    f.append("type",editType.value);
    f.append("batch",editBatch.value);
    f.append("price",editPrice.value);
    f.append("quantity",editQty.value);
    f.append("location",editLocation.value);
    f.append("expiry",editExpiry.value);
    fetch("",{method:"POST",body:f}).then(()=>location.reload());
}

function deleteMedicine(btn){
    if(!confirm("Delete this medicine?")) return;
    let f=new FormData();
    f.append("action","delete");
    f.append("id",btn.closest("tr").dataset.id);
    fetch("",{method:"POST",body:f}).then(()=>location.reload());
}
</script>

<?php include("../includes/footer.php"); ?>
</body>
</html>
