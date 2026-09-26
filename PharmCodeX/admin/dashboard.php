<?php
session_start();
include('../config/connection.php');

/* =========================
   AUTH CHECK
========================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin") {
    header("Location: ../index.php");
    exit();
}

$user_name = $_SESSION['name'] ?? "Admin";

/* =========================
   REAL DATA QUERIES
========================= */

// Total medicines
$total_medicines = $conn->query("SELECT COUNT(*) AS total FROM medicines")->fetch_assoc()['total'] ?? 0;

// Total sales
$total_sales = $conn->query("SELECT COUNT(*) AS total FROM sales")->fetch_assoc()['total'] ?? 0;

// Total pharmacists
$total_pharmacists = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='staff'")->fetch_assoc()['total'] ?? 0;

// Low stock (<50)
$low_stock = $conn->query("SELECT COUNT(*) AS total FROM medicines WHERE quantity < 50")->fetch_assoc()['total'] ?? 0;

// Greeting
$hour = date('H');
$greeting = ($hour < 12) ? "Good Morning" : (($hour < 18) ? "Good Afternoon" : "Good Evening");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard | PharmCodeX</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom -->
    <link rel="stylesheet" href="../css/style.css">

    <style>
        body { background: #f5f7fa; }
        .topbar {
            background: #fff;
            padding: 15px 30px;
            border-bottom: 1px solid #ddd;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .card-stats {
            border-radius: 12px;
            cursor: pointer;
            transition: 0.2s;
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }
        .card-stats i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    </style>
</head>

<body>

<div class="d-flex">

    <!-- Sidebar -->
    <?php include('../includes/sidebar.php'); ?>

    <!-- Main -->
    <div class="flex-grow-1 p-4">

        <!-- Topbar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Admin Dashboard</h5>
            <div>
                <span class="text-muted me-3"><?= $greeting ?>, <?= htmlspecialchars($user_name) ?></span>
                <strong id="datetime"></strong>
            </div>
        </div>

        <!-- Welcome -->
        <div class="mt-4 bg-white p-4 rounded shadow-sm d-flex justify-content-between">
            <div>
                <h4><?= $greeting ?>, <?= htmlspecialchars($user_name) ?> 👋</h4>
                <p class="text-muted mb-0">Here’s a quick overview of your pharmacy.</p>
            </div>
            <div id="weather" class="text-center">
                <i class="bi bi-cloud-sun-fill fs-1 text-warning"></i>
                <div id="temp">--°C</div>
                <div id="location">Loading...</div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row mt-4 g-4">

            <div class="col-md-3">
                <a href="../mutual/medicine.php" class="text-decoration-none text-dark">
                    <div class="card card-stats text-center p-3">
                        <i class="bi bi-capsule text-primary"></i>
                        <h6>Total Medicines</h6>
                        <h3><?= $total_medicines ?></h3>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="../mutual/sales.php" class="text-decoration-none text-dark">
                    <div class="card card-stats text-center p-3">
                        <i class="bi bi-cart text-success"></i>
                        <h6>Total Sales</h6>
                        <h3><?= $total_sales ?></h3>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="pharmacists.php" class="text-decoration-none text-dark">
                    <div class="card card-stats text-center p-3">
                        <i class="bi bi-person-badge text-info"></i>
                        <h6>Pharmacists</h6>
                        <h3><?= $total_pharmacists ?></h3>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="low_stock.php" class="text-decoration-none">
                    <div class="card card-stats text-center p-3 border border-danger">
                        <i class="bi bi-exclamation-triangle text-danger"></i>
                        <h6>Low Stock</h6>
                        <h3 class="text-danger"><?= $low_stock ?></h3>
                    </div>
                </a>
            </div>

        </div>

        <!-- Charts -->
        <div class="row mt-5 g-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h6>Monthly Sales</h6>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h6>Stock Overview</h6>
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include('../includes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* DateTime */
setInterval(() => {
    document.getElementById('datetime').innerText =
        new Date().toLocaleString();
}, 1000);

/* Weather */
fetch("https://api.openweathermap.org/data/2.5/weather?q=Karachi&units=metric&appid=YOUR_API_KEY")
.then(r => r.json())
.then(d => {
    if(!d.main){ document.getElementById('weather').style.display='none'; return; }
    document.getElementById('temp').innerText = Math.round(d.main.temp) + "°C";
    document.getElementById('location').innerText = d.name;
});

/* Charts */
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul'],
        datasets: [{
            data: [20,40,35,60,70,55,90],
            borderColor:'#0d6efd',
            backgroundColor:'rgba(13,110,253,0.1)',
            fill:true,
            tension:0.3
        }]
    },
    options:{plugins:{legend:{display:false}}}
});

new Chart(document.getElementById('stockChart'), {
    type:'bar',
    data:{
        labels:['Para','Amo','Aspi','Vit C','Ibu'],
        datasets:[{data:[50,30,20,40,15], backgroundColor:'#198754'}]
    },
    options:{plugins:{legend:{display:false}}}
});
</script>

</body>
</html>
