<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login-admin.php");
    exit();
}

// Update order status
if (isset($_POST['update_status'], $_POST['order_id'])) {
    $id = intval($_POST['order_id']);
    $status_options = ["Pending", "Completed", "Cancelled"];
    $status = in_array($_POST['update_status'], $status_options) ? $_POST['update_status'] : "Pending";

    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: orders-admin.php");
    exit();
}

// Fetch orders and items
$orders_query = $conn->query("
    SELECT o.*, u.name AS user_name, u.email, u.phone
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");

$orders_data = [];
$order_ids = [];
while ($row = $orders_query->fetch_assoc()) {
    $orders_data[$row['id']] = $row;
    $orders_data[$row['id']]['items'] = [];
    $order_ids[] = $row['id'];
}

if (!empty($order_ids)) {
    $ids_string = implode(',', array_map('intval', $order_ids));
    $items_query = $conn->query("
        SELECT oi.*, m.name AS menu_item_name
        FROM order_items oi
        JOIN menu_items m ON oi.item_id = m.id
        WHERE oi.order_id IN ($ids_string)
        ORDER BY oi.order_id, oi.id
    ");
    while ($item_row = $items_query->fetch_assoc()) {
        $orders_data[$item_row['order_id']]['items'][] = $item_row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Orders | BakerBest Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  display: flex;
  background: #fdf6f0;
  color: #333;
}
.sidebar {
  width: 240px;
  height: 100vh;
  background: linear-gradient(180deg, #7d3939, #a84747);
  color: white;
  position: fixed;
}
.sidebar.collapsed { width: 80px; }
.logo { display: flex; justify-content: space-between; align-items: center; padding: 20px; font-size: 20px; font-weight: bold; }
.toggle-btn { background: none; border: none; color: white; font-size: 20px; cursor: pointer; }
.menu { list-style: none; padding: 0; margin-top: 20px; }
.menu li { margin: 8px 0; }
.menu a { display: flex; align-items: center; text-decoration: none; color: white; padding: 12px 20px; border-radius: 8px; transition: 0.3s; }
.menu a:hover, .menu .active a { background: #f3961c; }
.icon { font-size: 20px; width: 30px; text-align: center; }
.text { margin-left: 10px; transition: 0.3s; }
.sidebar.collapsed .text { display: none; }
.main { margin-left: 240px; padding: 30px; width: calc(100% - 240px); transition: 0.3s; }
.sidebar.collapsed ~ .main { margin-left: 80px; }
.header h1 { color: #7d3939; margin-bottom: 25px; font-size: 28px; }

/* ORDER CARD STYLES */
.order-card { background: white; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); margin-bottom: 20px; overflow: hidden; transition: 0.3s; }
.order-card:hover { transform: translateY(-5px); }
.order-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; padding: 20px; align-items: center; border-bottom: 1px solid #eee; }
.order-summary div { font-size: 14px; }

.status-select {
  width: 100%;
  padding: 6px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-weight: 500;
  cursor: pointer;
  font-size: 14px;
  transition: background 0.3s, color 0.3s;
}

/* Status colors */
.status-pending { background: #f3961c; color:white; }
.status-completed { background: #7d3939; color:white; }
.status-cancelled { background: #ff534e; color:white; }

.order-items { padding: 15px 20px; background: #fafafa; display: none; animation: fadeIn 0.3s ease-in-out; }
.order-items ul { padding-left: 20px; margin: 5px 0; }
.order-items li { margin-bottom: 6px; font-size: 14px; }
.toggle-items-btn { margin-top: 10px; font-size: 13px; color: #7d3939; background: none; border: none; cursor: pointer; font-weight: 600; }
.toggle-items-btn:hover { text-decoration: underline; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

@media (max-width: 900px) {
  .main { margin-left: 0; width: 100%; padding: 20px; }
  .sidebar { display: none; }
}
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <div class="logo">
    <span class="logo-text">BakerBest</span>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
  </div>
  <ul class="menu">
    <li><a href="dashboard-admin.php"><span class="icon">🏠</span><span class="text">Dashboard</span></a></li>
    <li class="active"><a href="orders-admin.php"><span class="icon">📦</span><span class="text">Orders</span></a></li>
    <li><a href="customers-admin.php"><span class="icon">👥</span><span class="text">Customers</span></a></li>
    <li><a href="menu-admin.php"><span class="icon">🍰</span><span class="text">Menu</span></a></li>
    <li><a href="gallery-admin.php"><span class="icon">🖼️</span><span class="text">Gallery</span></a></li>
    <li><a href="contact-admin.php"><span class="icon">📩</span><span class="text">Contact</span></a></li>
    <li><a href="logout-admin.php"><span class="icon">🚪</span><span class="text">Logout</span></a></li>
  </ul>
</div>

<div class="main">
  <div class="header">
    <h1>Manage Orders</h1>
  </div>

  <?php if (!empty($orders_data)): ?>
    <?php foreach ($orders_data as $order): ?>
      <div class="order-card">
        <div class="order-summary">
          <div><strong>Order ID:</strong> <?= $order['id'] ?></div>
          <div><strong>Customer:</strong> <?= !empty($order['user_name']) ? htmlspecialchars($order['user_name']) : "Guest" ?></div>
          <div><strong>Contact:</strong> <?= !empty($order['email']) ? htmlspecialchars($order['email']) . '<br>' : "" ?><?= !empty($order['phone']) ? htmlspecialchars($order['phone']) : "-" ?></div>
          <div><strong>Total:</strong> Rs <?= number_format($order['total_amount'],2) ?></div>
          <div>
            <form method="POST" style="margin:0;">
              <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
              <select name="update_status" class="status-select" onchange="this.form.submit(); updateStatusColor(this)">
                <?php
                    $statuses = ['Pending', 'Completed', 'Cancelled'];
                    foreach ($statuses as $st):
                        $selected = $order['status'] == $st ? 'selected' : '';
                        echo "<option value='$st' $selected>$st</option>";
                    endforeach;
                ?>
              </select>
            </form>
          </div>
          <div><strong>Date:</strong> <?= date("M d, Y H:i", strtotime($order['created_at'])) ?></div>
        </div>

        <?php if (!empty($order['special_message']) || !empty($order['items'])): ?>
          <button class="toggle-items-btn" onclick="toggleItems(this)">View Details ▼</button>
          <div class="order-items">
            <?php if (!empty($order['special_message'])): ?>
              <p><strong>Message:</strong> <?= htmlspecialchars($order['special_message']) ?></p>
            <?php endif; ?>
            <?php if (!empty($order['items'])): ?>
              <p><strong>Items:</strong></p>
              <ul>
                <?php foreach ($order['items'] as $item): ?>
                  <li><?= intval($item['quantity']) ?> x <?= htmlspecialchars($item['menu_item_name']) ?> (Rs <?= number_format($item['price_at_order'], 2) ?> each) = Rs <?= number_format($item['line_total'], 2) ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No orders found.</p>
  <?php endif; ?>
</div>

<script>
function toggleSidebar() { document.getElementById("sidebar").classList.toggle("collapsed"); }
function toggleItems(btn){
    const itemsDiv = btn.nextElementSibling;
    if(itemsDiv.style.display === "block"){
        itemsDiv.style.display = "none"; btn.textContent = "View Details ▼";
    } else {
        itemsDiv.style.display = "block"; btn.textContent = "Hide Details ▲";
    }
}

// Set dropdown color based on selected value
function updateStatusColor(select) {
    const value = select.value.toLowerCase();
    select.classList.remove("status-pending","status-completed","status-cancelled");
    if(value === "pending") select.classList.add("status-pending");
    else if(value === "completed") select.classList.add("status-completed");
    else if(value === "cancelled") select.classList.add("status-cancelled");
}

// Initialize colors on page load
document.querySelectorAll('.status-select').forEach(function(sel){
    updateStatusColor(sel);
});
</script>

</body>
</html>
