<?php
session_start();
require_once 'admin/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch menu items
$menu_items = $conn->query("
    SELECT m.*, c.name AS category_name 
    FROM menu_items m
    LEFT JOIN categories c ON m.category_id = c.id 
    ORDER BY c.name ASC, m.name ASC
");

$menu = [];
while ($row = $menu_items->fetch_assoc()) {
    $menu[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Order | BakerBest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/order.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="order-container">
    <h1>Online Order</h1>
    <form id="orderForm">
        <table class="table" id="orderTable">
            <thead>
                <tr>
                    <th>Menu Item</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody id="orderBody"></tbody>
        </table>
        <button type="button" class="add-btn" onclick="addRow()">+ Add Item</button>
        <div class="total-box" id="grandTotalBox">Grand Total: Rs 0.00</div>

        <label>Special Message (Optional)</label>
        <textarea name="message" class="message-textarea"></textarea>

        <button type="button" class="confirm-btn" onclick="confirmOrder()">Confirm Order</button>

        <div class="order-actions">
            <a href="index.php" class="exit-btn">Exit</a>
            <a href="menu.php" class="home-btn">Return to Menu</a>
        </div>
    </form>
</section>

<!-- Thank You Modal -->
<div id="thankyouModal">
    <div class="modal-content">
        <h2>BakerBest 🍰</h2>
        <h2>Thank You!</h2>
        <p>Your order has been successfully submitted.</p>
        <button onclick="closeModal()">OK</button>
    </div>
</div>

<script>
let menuData = <?= json_encode($menu, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>
<script src="assets/js/order.js"></script>

<?php include 'footer.php'; ?>
</body>
</html>
