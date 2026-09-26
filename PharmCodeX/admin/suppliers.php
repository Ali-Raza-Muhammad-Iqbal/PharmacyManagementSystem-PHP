<?php
// include('../auth/session.php');
include('../config/connection.php');

/* =========================
   ADD / UPDATE / DELETE
========================= */
if (isset($_POST['action'])) {

    // ADD SUPPLIER
    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare(
            "INSERT INTO suppliers (name, company, phone, email, address)
             VALUES (?,?,?,?,?)"
        );
        $stmt->bind_param(
            "sssss",
            $_POST['name'],
            $_POST['company'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['address']
        );
        $stmt->execute();
        exit;
    }

    // UPDATE SUPPLIER
    if ($_POST['action'] === 'update') {
        $stmt = $conn->prepare(
            "UPDATE suppliers SET
                name=?, company=?, phone=?, email=?, address=?
             WHERE id=?"
        );
        $stmt->bind_param(
            "sssssi",
            $_POST['name'],
            $_POST['company'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['address'],
            $_POST['id']
        );
        $stmt->execute();
        exit;
    }

    // DELETE SUPPLIER
    if ($_POST['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM suppliers WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        exit;
    }
}

/* =========================
   FETCH SUPPLIERS
========================= */
$result = $conn->query("SELECT * FROM suppliers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Suppliers | Pharmacy System</title>
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
    <h6 class="mb-0">Suppliers</h6>
    <button class="btn btn-sm"
        style="background-color: var(--primary-color); color:#fff"
        data-bs-toggle="modal" data-bs-target="#addSupplierModal">
        + Add Supplier
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
    <th>Name</th>
    <th>Company</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Address</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php $i=1; while($row=$result->fetch_assoc()): ?>
<tr data-id="<?= $row['id'] ?>">
    <td><?= $i++ ?></td>
    <td class="name"><?= htmlspecialchars($row['name']) ?></td>
    <td class="company"><?= htmlspecialchars($row['company']) ?></td>
    <td class="phone"><?= htmlspecialchars($row['phone']) ?></td>
    <td class="email"><?= htmlspecialchars($row['email']) ?></td>
    <td class="address"><?= htmlspecialchars($row['address']) ?></td>
    <td>
        <button class="btn btn-sm btn-outline-primary" onclick="openEditModal(this)">Edit</button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteSupplier(this)">Delete</button>
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

<!-- ADD SUPPLIER MODAL -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
<h5>Add Supplier</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input class="form-control mb-2" id="newName" placeholder="Supplier Name">
<input class="form-control mb-2" id="newCompany" placeholder="Company Name">
<input class="form-control mb-2" id="newPhone" placeholder="Phone">
<input class="form-control mb-2" id="newEmail" placeholder="Email">
<textarea class="form-control mb-2" id="newAddress" placeholder="Address"></textarea>
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn" style="background:var(--primary-color);color:#fff"
onclick="addSupplier()">Save</button>
</div>
</div>
</div>
</div>

<!-- EDIT SUPPLIER MODAL -->
<div class="modal fade" id="editSupplierModal" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
<h5>Edit Supplier</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input class="form-control mb-2" id="editName">
<input class="form-control mb-2" id="editCompany">
<input class="form-control mb-2" id="editPhone">
<input class="form-control mb-2" id="editEmail">
<textarea class="form-control mb-2" id="editAddress"></textarea>
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn" style="background:var(--primary-color);color:#fff"
onclick="updateSupplier()">Update</button>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let editRow;

function addSupplier(){
    let f=new FormData();
    f.append("action","add");
    f.append("name",newName.value);
    f.append("company",newCompany.value);
    f.append("phone",newPhone.value);
    f.append("email",newEmail.value);
    f.append("address",newAddress.value);
    fetch("",{method:"POST",body:f}).then(()=>location.reload());
}

function openEditModal(btn){
    editRow=btn.closest("tr");
    editName.value=editRow.querySelector(".name").innerText;
    editCompany.value=editRow.querySelector(".company").innerText;
    editPhone.value=editRow.querySelector(".phone").innerText;
    editEmail.value=editRow.querySelector(".email").innerText;
    editAddress.value=editRow.querySelector(".address").innerText;
    new bootstrap.Modal(editSupplierModal).show();
}

function updateSupplier(){
    let f=new FormData();
    f.append("action","update");
    f.append("id",editRow.dataset.id);
    f.append("name",editName.value);
    f.append("company",editCompany.value);
    f.append("phone",editPhone.value);
    f.append("email",editEmail.value);
    f.append("address",editAddress.value);
    fetch("",{method:"POST",body:f}).then(()=>location.reload());
}

function deleteSupplier(btn){
    if(!confirm("Delete this supplier?")) return;
    let f=new FormData();
    f.append("action","delete");
    f.append("id",btn.closest("tr").dataset.id);
    fetch("",{method:"POST",body:f}).then(()=>location.reload());
}
</script>

<?php include("../includes/footer.php"); ?>
</body>
</html>
