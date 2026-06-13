<?php
session_start();
require_once "db_connection.php";

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login-admin.php");
    exit();
}

/* ======================
   DASHBOARD DATA
======================*/

// Total Orders
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'] ?? 0;

// Total Customers
$totalCustomers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;

// Menu Items
$totalMenu = $conn->query("SELECT COUNT(*) AS total FROM menu_items")->fetch_assoc()['total'] ?? 0;

// Gallery Images
$totalGallery = $conn->query("SELECT COUNT(*) AS total FROM gallery_images")->fetch_assoc()['total'] ?? 0;

// Contact Messages
$totalMessages = $conn->query("SELECT COUNT(*) AS total FROM contact_messages")->fetch_assoc()['total'] ?? 0;


/* ======================
   SALES USING created_at
======================*/

// Daily Sales
$dailySales = $conn->query("
    SELECT IFNULL(SUM(total_amount),0) AS total 
    FROM orders 
    WHERE DATE(created_at) = CURDATE()
")->fetch_assoc()['total'] ?? 0;

// Weekly Sales
$weeklySales = $conn->query("
    SELECT IFNULL(SUM(total_amount),0) AS total 
    FROM orders 
    WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
")->fetch_assoc()['total'] ?? 0;

// Monthly Sales
$monthlySales = $conn->query("
    SELECT IFNULL(SUM(total_amount),0) AS total 
    FROM orders 
    WHERE MONTH(created_at) = MONTH(CURDATE())
    AND YEAR(created_at) = YEAR(CURDATE())
")->fetch_assoc()['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | BakerBest</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* GLOBAL */
body {
  margin: 0;
  font-family: Arial, sans-serif;
  display: flex;
  background: #fdf6f0;
}

.sidebar {
  width: 240px;
  height: 100vh;
  background: linear-gradient(180deg, #7d3939, #a84747);
  color: white;
  position: fixed;
  transition: 0.3s;
  overflow: hidden;
}

.sidebar.collapsed {
  width: 80px;
}

.logo {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  font-size: 20px;
}

.toggle-btn {
  background: none;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
}

.menu {
  list-style: none;
  padding: 0;
}

.menu li {
  margin: 10px 0;
}

.menu a {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: white;
  padding: 12px 20px;
  transition: 0.3s;
}

.menu a:hover {
  background: #f3961c;
  border-radius: 8px;
}

.icon {
  font-size: 20px;
  width: 30px;
  text-align: center;
}

.text {
  margin-left: 10px;
  transition: 0.3s;
}

.sidebar.collapsed .text {
  display: none;
}

.sidebar.collapsed .logo-text {
  display: none;
}

.main {
  margin-left: 240px;
  transition: 0.3s;
}

.sidebar.collapsed ~ .main {
  margin-left: 80px;
}


/* MAIN */
.main {
  margin-left: 240px;
  padding: 30px;
  width: calc(100% - 240px);
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header h1 {
  color: #7d3939;
}

/* SALES SUMMARY */
.sales-summary {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-top: 25px;
}

.sales-card {
  background: white;
  padding: 20px;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  text-align: center;
}

.sales-card h4 {
  color: #7d3939;
}

.sales-card p {
  font-size: 22px;
  font-weight: bold;
  color: #f3961c;
}

/* STATS CARDS */
.cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-top: 30px;
}

.card {
  background: white;
  padding: 20px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: 0.3s;
}

.card:hover {
  transform: translateY(-5px);
}

.card h3 {
  color: #7d3939;
}

.card p {
  font-size: 24px;
  font-weight: bold;
}

/* CHART */
.chart-container {
  margin-top: 40px;
  background: white;
  padding: 25px;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* RESPONSIVE */
@media (max-width: 900px) {
  .sales-summary,
  .cards {
    grid-template-columns: repeat(1, 1fr);
  }

  .sidebar {
    display: none;
  }

  .main {
    margin-left: 0;
    width: 100%;
  }
}

</style>
</head>
<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("collapsed");
}
</script>

<body>

<div class="sidebar" id="sidebar">
    <div class="logo">
        <span class="logo-text">BakerBest</span>
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    </div>

    <ul class="menu">
        <li class="active">
            <a href="dashboard-admin.php">
                <span class="icon">🏠</span>
                <span class="text">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="orders-admin.php">
                <span class="icon">📦</span>
                <span class="text">Orders</span>
            </a>
        </li>

        <li>
            <a href="customers-admin.php">
                <span class="icon">👥</span>
                <span class="text">Customers</span>
            </a>
        </li>

        <li>
            <a href="menu-admin.php">
                <span class="icon">🍰</span>
                <span class="text">Menu</span>
            </a>
        </li>

        <li>
            <a href="gallery-admin.php">
                <span class="icon">🖼️</span>
                <span class="text">Gallery</span>
            </a>
        </li>

        <li>
            <a href="contact-admin.php">
                <span class="icon">📩</span>
                <span class="text">Contact</span>
            </a>
        </li>

        <li>
            <a href="logout-admin.php" class="logout">
                <span class="icon">🚪</span>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</div>


<div class="main">

    <div class="header">
        <h1>Dashboard Overview</h1>
        <div>Welcome, Admin</div>
    </div>

    <!-- Sales Summary -->
    <div class="sales-summary">
        <div class="sales-card">
            <h4>Daily Sales</h4>
            <p>Rs. <?= number_format($dailySales,2) ?></p>
        </div>
        <div class="sales-card">
            <h4>Weekly Sales</h4>
            <p>Rs. <?= number_format($weeklySales,2) ?></p>
        </div>
        <div class="sales-card">
            <h4>Monthly Sales</h4>
            <p>Rs. <?= number_format($monthlySales,2) ?></p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="cards">
        <div class="card">
            <h3>Total Orders</h3>
            <p><?= $totalOrders ?></p>
        </div>
        <div class="card">
            <h3>Total Customers</h3>
            <p><?= $totalCustomers ?></p>
        </div>
        <div class="card">
            <h3>Menu Items</h3>
            <p><?= $totalMenu ?></p>
        </div>
        <div class="card">
            <h3>Gallery Images</h3>
            <p><?= $totalGallery ?></p>
        </div>
        <div class="card">
            <h3>Contact Messages</h3>
            <p><?= $totalMessages ?></p>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="chart-container">
        <canvas id="salesChart"></canvas>
    </div>

</div>

<script>
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: ['Daily', 'Weekly', 'Monthly'],
        datasets: [{
            data: [
                <?= $dailySales ?>,
                <?= $weeklySales ?>,
                <?= $monthlySales ?>
            ],
            borderColor: '#a84747',
            backgroundColor: 'rgba(243,150,28,0.2)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});
</script>

</body>
</html>
