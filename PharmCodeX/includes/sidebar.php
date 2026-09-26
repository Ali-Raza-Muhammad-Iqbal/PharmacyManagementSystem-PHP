<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<div class="sidebar">

    <!-- LOGO SECTION -->
    <div class="text-center py-3 border-bottom">

        <h5 class="text-white mb-0">PharmCodeX</h5>
    </div>
 <?php
// Determine the dashboard link based on user role
$dashboardLink = "#"; // default fallback
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        $dashboardLink = "../admin/dashboard.php";
    } elseif ($_SESSION['role'] === 'staff') {
        $dashboardLink = "../pharmacist/pharmacist_dashboard.php";
    }
}

// Determine active page
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<a href="<?= $dashboardLink ?>" 
   class="<?= ($currentPage == basename($dashboardLink)) ? 'active' : '' ?>">
    <i class="bi bi-speedometer2 me-2"></i> Dashboard
</a>

    <a href="../mutual/medicine.php"
       class="<?= ($currentPage == 'medicine.php') ? 'active' : '' ?>">
        <i class="bi bi-capsule me-2"></i> Medicines
    </a>

    <a href="../mutual/customers.php"
       class="<?= ($currentPage == 'customers.php') ? 'active' : '' ?>">
        <i class="bi bi-people me-2"></i> Customers
    </a>

    <?php if ($_SESSION['role'] === "admin") { ?>

        <a href="../admin/pharmacists.php"
           class="<?= ($currentPage == 'pharmacists.php') ? 'active' : '' ?>">
            <i class="bi bi-person-badge me-2"></i> Pharmacists
        </a>

        <a href="../admin/suppliers.php"
           class="<?= ($currentPage == 'suppliers.php') ? 'active' : '' ?>">
            <i class="bi bi-truck me-2"></i> Suppliers
        </a>

        <a href="../mutual/reports.php"
           class="<?= ($currentPage == 'reports.php') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text me-2"></i> Reports
        </a>

        <a href="../admin/backup_restore.php"
            class="<?= ($currentPage == 'backup_restore.php') ? 'active' : '' ?>">
            <i class="bi bi-cloud-arrow-up me-2"></i> Backup & Restore
        </a>


    <?php } ?>

    <a href="../mutual/sales.php"
       class="<?= ($currentPage == 'sales.php') ? 'active' : '' ?>">
        <i class="bi bi-cart me-2"></i> Sales
    </a>

    <a href="../mutual/purchase.php"
   class="<?= ($currentPage == 'purchase.php') ? 'active' : '' ?>">
    <i class="bi bi-bag-plus me-2"></i> Purchase
    </a>

      <a href="../mutual/low.php"
   class="<?= ($currentPage == 'low.php') ? 'active' : '' ?>">
   <i class="bi bi-exclamation-triangle me-2"></i> Low Stock
    </a>


    <a href="../auth/logout.php">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>

</div>

</body>
</html>
