<?php
include 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);

$customer_id = $data['customer_id'];
$cart = $data['cart'];
$payment_mode = $data['payment_mode'];
$delivery_mode = $data['delivery_mode'];
$preferred_time = $data['preferred_time'];
$order_status = 'Pending';
$order_date = date('Y-m-d H:i:s');

// Calculate total
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Insert into orders table
$stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount, payment_mode, delivery_mode, preferred_time, order_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("idsssss", $customer_id, $total, $payment_mode, $delivery_mode, $preferred_time, $order_date, $order_status);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// Insert into order_items table
foreach ($cart as $item) {
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $order_id, $item['productId'], $item['quantity'], $item['price']);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(["success" => true, "order_id" => $order_id]);
?>
