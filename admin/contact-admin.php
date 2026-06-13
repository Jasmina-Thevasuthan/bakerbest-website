<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login-admin.php");
    exit();
}

/* DELETE MESSAGE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        header("Location: contact-admin.php?deleted=1");
        exit();
    } else {
        header("Location: contact-admin.php?error=1");
        exit();
    }
}

// Fetch all messages
$messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin | Contact Messages</title>
<link rel="icon" href="../images/logo.png" type="image/png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ======================
   GLOBAL & SIDEBAR
====================== */
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
.sidebar.collapsed { width: 80px; }
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
.menu li { margin: 10px 0; }
.menu a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: white;
    padding: 12px 20px;
    transition: 0.3s;
}
.menu a:hover { background: #f3961c; border-radius: 8px; }
.menu li.active a { background: #f3961c; border-radius: 8px; }
.icon { font-size: 20px; width: 30px; text-align: center; }
.text { margin-left: 10px; transition: 0.3s; }
.sidebar.collapsed .text { display: none; }
.sidebar.collapsed .logo-text { display: none; }

/* ======================
   MAIN CONTENT
====================== */
.main {
    margin-left: 240px;
    padding: 30px;
    width: calc(100% - 240px);
    transition: 0.3s;
}
.sidebar.collapsed ~ .main { margin-left: 80px; }
.header { display: flex; justify-content: space-between; align-items: center; }
.header h1 { color: #7d3939; }

/* ALERTS */
.alert {
    padding: 12px 20px;
    border-radius: 10px;
    margin: 15px 0;
    font-weight: bold;
}
.alert.success { background: #d4edda; color: #155724; }
.alert.error { background: #f8d7da; color: #721c24; }

/* TABLE CARD */
.table-card {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    overflow-x: auto;
    margin-top: 20px;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}
th, td {
    padding: 12px 15px;
    text-align: left;
    vertical-align: middle;
}
th {
    background: #a84747;
    color: white;
    font-weight: bold;
    text-transform: uppercase;
}
tr:nth-child(even) { background: #f9f9f9; }

/* DELETE BUTTON */
.btn-del {
    background: #d32f2f;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 6px 10px;
    cursor: pointer;
}
.btn-del:hover { background: #f44336; }

/* RESPONSIVE */
@media (max-width: 900px) {
    .main { margin-left: 0; width: 100%; padding: 15px; }
    .table-card { padding: 10px; }
    table, th, td { font-size: 14px; }
}
</style>

<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("collapsed");
}
</script>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="logo">
        <span class="logo-text">BakerBest</span>
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    </div>
    <ul class="menu">
        <li><a href="dashboard-admin.php"><span class="icon">🏠</span><span class="text">Dashboard</span></a></li>
        <li><a href="orders-admin.php"><span class="icon">📦</span><span class="text">Orders</span></a></li>
        <li><a href="customers-admin.php"><span class="icon">👥</span><span class="text">Customers</span></a></li>
        <li><a href="menu-admin.php"><span class="icon">🍰</span><span class="text">Menu</span></a></li>
        <li><a href="gallery-admin.php"><span class="icon">🖼️</span><span class="text">Gallery</span></a></li>
        <li class="active"><a href="contact-admin.php"><span class="icon">📩</span><span class="text">Contact</span></a></li>
        <li><a href="logout-admin.php"><span class="icon">🚪</span><span class="text">Logout</span></a></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <div class="header">
        <h1>Contact Messages</h1>
        <div>Welcome, Admin</div>
    </div>

    <!-- Alerts -->
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert success">Message deleted successfully!</div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert error">Failed to delete the message. Try again.</div>
    <?php endif; ?>

    <!-- TABLE -->
    <?php if ($messages->num_rows > 0): ?>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date Sent</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($msg = $messages->fetch_assoc()): ?>
                <tr>
                    <td><?= intval($msg['id']) ?></td>
                    <td><?= htmlspecialchars($msg['name']) ?></td>
                    <td><?= htmlspecialchars($msg['email']) ?></td>
                    <td><?= htmlspecialchars($msg['subject']) ?></td>
                    <td style="max-width: 250px; word-wrap: break-word;"><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                    <td><?= htmlspecialchars(date("Y-m-d H:i", strtotime($msg['created_at']))) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete this message?');">
                            <input type="hidden" name="delete_id" value="<?= intval($msg['id']) ?>">
                            <button type="submit" class="btn-del"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p>No messages found.</p>
    <?php endif; ?>
</div>

</body>
</html>
