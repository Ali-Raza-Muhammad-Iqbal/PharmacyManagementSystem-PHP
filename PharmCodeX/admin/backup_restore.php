<?php
// include('../auth/session.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../admin/dashboard.php");
    exit();
}

include('../config/connection.php');

/* =========================
   BACKUP / RESTORE ACTIONS
========================= */
$backupDir = "../backups/";
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

if (isset($_POST['action'])) {

    $dbHost = "localhost";
    $dbUser = "root";
    $dbPass = "";
    $dbName = "pharmaco";

    // BACKUP
    if ($_POST['action'] === 'backup') {

        $fileName = $backupDir . $dbName . "_" . date("Y-m-d_H-i-s") . ".sql";
        $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > {$fileName}";
        system($command);

        exit;
    }

    // RESTORE
    if ($_POST['action'] === 'restore') {

        if (!empty($_FILES['sql_file']['tmp_name'])) {
            $tmpFile = $_FILES['sql_file']['tmp_name'];
            $command = "mysql --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} < {$tmpFile}";
            system($command);
        }

        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Backup & Restore | Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root { --primary-color: #0d6efd; }
body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
.topbar { height: 60px; background: #fff; border-bottom: 1px solid #dee2e6; }
.content { padding: 20px; }
.card { border-radius: 12px; }
</style>
</head>

<body>
<div class="d-flex">

<?php include('../includes/sidebar.php'); ?>

<div class="flex-grow-1">

<!-- TOPBAR -->
<div class="topbar d-flex align-items-center px-4">
    <h6 class="mb-0">
        <i class="bi bi-cloud-arrow-up-down me-2"></i> Backup & Restore
    </h6>
</div>

<!-- CONTENT -->
<div class="content">
<div class="row g-4">

    <!-- BACKUP -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-cloud-arrow-up me-2"></i> Database Backup
            </div>
            <div class="card-body">
                <button class="btn btn-primary" onclick="createBackup()">
                    <i class="bi bi-download me-1"></i> Create Backup
                </button>
            </div>
        </div>
    </div>

    <!-- RESTORE -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <i class="bi bi-cloud-arrow-down me-2"></i> Restore Database
            </div>
            <div class="card-body">
                <input type="file" id="sqlFile" class="form-control mb-3" accept=".sql">
                <button class="btn btn-success" onclick="restoreDatabase()">
                    <i class="bi bi-upload me-1"></i> Restore Backup
                </button>
            </div>
        </div>
    </div>

</div>
</div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function createBackup(){
    let f = new FormData();
    f.append("action","backup");
    fetch("",{method:"POST",body:f})
        .then(()=>alert("Backup created successfully"))
}

function restoreDatabase(){
    let file = document.getElementById("sqlFile").files[0];
    if(!file){
        alert("Please select a SQL file");
        return;
    }
    if(!confirm("Are you sure you want to restore the database?")) return;

    let f = new FormData();
    f.append("action","restore");
    f.append("sql_file",file);
    fetch("",{method:"POST",body:f})
        .then(()=>alert("Database restored successfully"))
}
</script>

<?php include('../includes/footer.php'); ?>
</body>
</html>
