<?php
// include('../auth/session.php');
include('../config/connection.php');

/* =========================
   ADD / UPDATE / DELETE
========================= */
if (isset($_POST['action'])) {

    // ADD
    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare(
            "INSERT INTO users (name,email,phone,username,password,role)
             VALUES (?,?,?,?,?,'pharmacist')"
        );
        $stmt->bind_param(
            "sssss",
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['username'],
            $_POST['password']
        );
        $stmt->execute();
        exit;
    }

    // UPDATE
    if ($_POST['action'] === 'update') {
        $stmt = $conn->prepare(
            "UPDATE users SET name=?, email=?, phone=?, username=?, password=? WHERE id=?"
        );
        $stmt->bind_param(
            "sssssi",
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['username'],
            $_POST['password'],
            $_POST['id']
        );
        $stmt->execute();
        exit;
    }

    // DELETE (ADMIN PROTECTED)
    if ($_POST['action'] === 'delete') {

        // Check role first
        $check = $conn->prepare("SELECT role FROM users WHERE id=?");
        $check->bind_param("i", $_POST['id']);
        $check->execute();
        $roleRow = $check->get_result()->fetch_assoc();

        // Block admin deletion
        if ($roleRow['role'] === 'admin') {
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        exit;
    }
}

/* =========================
   FETCH USERS
========================= */
$sql = "SELECT * FROM users ORDER BY role ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pharmacist Management | Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root { --primary-color: #0d6efd; }
body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
.sidebar { width: 250px; min-height: 100vh; background: #212529; }
.sidebar a { color: #adb5bd; text-decoration: none; padding: 12px 20px; display: block; }
.sidebar a:hover, .sidebar a.active { background: var(--primary-color); color: #fff; }
.topbar { height: 60px; background: #fff; border-bottom: 1px solid #dee2e6; }
.content { padding: 20px; }
.card { border-radius: 12px; }
</style>
</head>

<body>
<div class="d-flex">

<?php include('../includes/sidebar.php'); ?>

<div class="flex-grow-1">

<div class="topbar d-flex align-items-center justify-content-between px-4">
    <h6 class="mb-0">Pharmacist Management</h6>
    <button class="btn btn-sm" style="background:var(--primary-color);color:#fff"
            data-bs-toggle="modal" data-bs-target="#addPharmacistModal">
        + Add Pharmacist
    </button>
</div>

<div class="content">
<div class="card shadow-sm">
<div class="card-body">

<table class="table table-hover align-middle" id="pharmaTable">
<thead class="table-light">
<tr>
    <th>#</th>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Username</th>
    <th>Password</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php $i=1; while($row=$result->fetch_assoc()): ?>
<tr data-id="<?= $row['id'] ?>">
    <td><?= $i++ ?></td>
    <td class="pname"><?= htmlspecialchars($row['name']) ?></td>
    <td class="pemail"><?= htmlspecialchars($row['email']) ?></td>
    <td class="pphone"><?= htmlspecialchars($row['phone']) ?></td>
    <td class="puser"><?= htmlspecialchars($row['username']) ?></td>
    <td class="ppassword"><?= htmlspecialchars($row['password']) ?></td>
    <td>
        <button class="btn btn-sm btn-outline-primary"
                onclick="openEditPharmacist(this)">Edit</button>

        <?php if ($row['role'] !== 'admin') { ?>
            <button class="btn btn-sm btn-outline-danger"
                    onclick="deletePharmacist(this)">Delete</button>
        <?php } else { ?>
            <button class="btn btn-sm btn-outline-danger" disabled
                    title="Admin account cannot be deleted">
                Delete
            </button>
        <?php } ?>
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

<!-- ADD MODAL -->
<div class="modal fade" id="addPharmacistModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
<h5>Add Pharmacist</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input class="form-control mb-2" id="newPName" placeholder="Name">
<input class="form-control mb-2" id="newPEmail" placeholder="Email">
<input class="form-control mb-2" id="newPPhone" placeholder="Phone">
<input class="form-control mb-2" id="newPUser" placeholder="Username">
<input class="form-control mb-2" id="newPPassword" placeholder="Password">
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn btn-primary" onclick="addPharmacist()">Save</button>
</div>
</div>
</div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editPharmacistModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
<h5>Edit Pharmacist</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input class="form-control mb-2" id="editPName">
<input class="form-control mb-2" id="editPEmail">
<input class="form-control mb-2" id="editPPhone">
<input class="form-control mb-2" id="editPUser">
<input class="form-control mb-2" id="editPPassword">
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn btn-primary" onclick="updatePharmacist()">Update</button>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let editRow;

function addPharmacist(){
    let f=new FormData();
    f.append("action","add");
    f.append("name",newPName.value);
    f.append("email",newPEmail.value);
    f.append("phone",newPPhone.value);
    f.append("username",newPUser.value);
    f.append("password",newPPassword.value);
    fetch("",{method:"POST",body:f}).then(()=>location.reload());
}

function openEditPharmacist(btn){
    editRow=btn.closest("tr");
    editPName.value=editRow.querySelector(".pname").innerText;
    editPEmail.value=editRow.querySelector(".pemail").innerText;
    editPPhone.value=editRow.querySelector(".pphone").innerText;
    editPUser.value=editRow.querySelector(".puser").innerText;
    editPPassword.value=editRow.querySelector(".ppassword").innerText;
    new bootstrap.Modal(editPharmacistModal).show();
}

function updatePharmacist(){
    let f=new FormData();
    f.append("action","update");
    f.append("id",editRow.dataset.id);
    f.append("name",editPName.value);
    f.append("email",editPEmail.value);
    f.append("phone",editPPhone.value);
    f.append("username",editPUser.value);
    f.append("password",editPPassword.value);
    fetch("",{method:"POST",body:f}).then(()=>location.reload());
}

function deletePharmacist(btn){
    if(!confirm("Delete this pharmacist?"))return;
    let f=new FormData();
    f.append("action","delete");
    f.append("id",btn.closest("tr").dataset.id);
    fetch("",{method:"POST",body:f}).then(()=>location.reload());
}
</script>

<?php include('../includes/footer.php'); ?>
</body>
</html>
