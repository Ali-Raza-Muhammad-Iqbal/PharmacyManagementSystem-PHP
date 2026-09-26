<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include('../config/connection.php');

/* =========================
   ADD CUSTOMER
========================= */
if (isset($_POST['add_customer'])) {
    $stmt = $conn->prepare(
        "INSERT INTO customers (name, phone, email, address) VALUES (?,?,?,?)"
    );
    $stmt->bind_param(
        "ssss",
        $_POST['name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['address']
    );
    $stmt->execute();
    header("Location: customers.php");
    exit();
}

/* =========================
   UPDATE CUSTOMER
========================= */
if (isset($_POST['update_customer'])) {
    $stmt = $conn->prepare(
        "UPDATE customers SET name=?, phone=?, email=?, address=? WHERE id=?"
    );
    $stmt->bind_param(
        "ssssi",
        $_POST['name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['address'],
        $_POST['id']
    );
    $stmt->execute();
    header("Location: customers.php");
    exit();
}

/* =========================
   DELETE CUSTOMER
========================= */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM customers WHERE id=$id");
    header("Location: customers.php");
    exit();
}

$customers = $conn->query("SELECT * FROM customers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customers | Pharmacy System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Fix for modal display */
        .modal {
            z-index: 1050;
        }
        .modal-backdrop {
            z-index: 1040;
        }
        .modal-dialog {
            margin: 1.75rem auto;
        }
    </style>
</head>

<body class="admin-theme">

<div class="d-flex">

    <?php include('../includes/sidebar.php'); ?>

    <div class="flex-grow-1">


        <div class="content p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Customers</h5>
                <button class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#addModal">
                    + Add Customer
                </button>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">

                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th width="150">Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php $i=1; while($row=$customers->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#edit<?= $row['id'] ?>">
                                    Edit
                                </button>

                                <a href="?delete=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this customer?')">
                                    Delete
                                </a>
                            </td>
                        </tr>

                        <!-- EDIT MODAL -->
                        <div class="modal fade" id="edit<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Customer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <div class="mb-3">
                                                <label for="edit_name_<?= $row['id'] ?>" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="edit_name_<?= $row['id'] ?>" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_phone_<?= $row['id'] ?>" class="form-label">Phone</label>
                                                <input type="text" class="form-control" id="edit_phone_<?= $row['id'] ?>" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_email_<?= $row['id'] ?>" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="edit_email_<?= $row['id'] ?>" name="email" value="<?= htmlspecialchars($row['email']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_address_<?= $row['id'] ?>" class="form-label">Address</label>
                                                <textarea class="form-control" id="edit_address_<?= $row['id'] ?>" name="address" rows="2"><?= htmlspecialchars($row['address']) ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="update_customer" class="btn btn-success">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <?php endwhile; ?>

                        </tbody>
                    </table>

                </div>
            </div>

        </div>

        

    </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="add_name" name="name" placeholder="Customer Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="add_phone" name="phone" placeholder="Phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="add_email" name="email" placeholder="Email">
                    </div>
                    <div class="mb-3">
                        <label for="add_address" class="form-label">Address</label>
                        <textarea class="form-control" id="add_address" name="address" placeholder="Address" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_customer" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

<!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Ensure modals work properly
document.addEventListener('DOMContentLoaded', function() {
    // This ensures Bootstrap's modal functionality works
    var modalElements = document.querySelectorAll('.modal');
    modalElements.forEach(function(modal) {
        new bootstrap.Modal(modal);
    });
});
</script>
</body>
</html>