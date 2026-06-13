<?php
session_start();
require_once 'admin/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!isset($data['items']) || !is_array($data['items']) || empty($data['items']) || !isset($data['grand_total'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid order data received.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$grand_total = floatval($data['grand_total']);
$message = $data['message'] ?? '';
$order_status = 'Pending';

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, special_message, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $user_id, $grand_total, $message, $order_status);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();

    // Insert into the order_items table
    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price_at_order, line_total) VALUES (?, ?, ?, ?, ?)");

    foreach ($data['items'] as $item) {
        $item_id = $item['item_id'];
        $quantity = $item['quantity'];
        $price_at_order = floatval($item['price_at_order']);
        $line_total = floatval($item['line_total']);

        $item_stmt->bind_param("iiidd", $order_id, $item_id, $quantity, $price_at_order, $line_total);
        $item_stmt->execute();
    }

    $item_stmt->close();

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Order submitted successfully.', 'order_id' => $order_id]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Order Submission Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>