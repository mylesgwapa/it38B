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

try {
    // Start transaction
    $conn->begin_transaction();

    // 1. Check stock availability for all products
    foreach ($cart as $item) {
        $stmt = $conn->prepare("SELECT quantity FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $item['product_id']);
        $stmt->execute();
        $stmt->bind_result($stock_quantity);
        $stmt->fetch();
        $stmt->close();

        if ($stock_quantity < $item['quantity']) {
            // Not enough stock for this product
            throw new Exception("Insufficient stock for product ID " . $item['product_id']);
        }
    }

    // 2. Calculate total
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // 3. Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount, payment_mode, delivery_mode, preferred_time, order_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idsssss", $customer_id, $total, $payment_mode, $delivery_mode, $preferred_time, $order_date, $order_status);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // 4. Insert into order_items and update product stock
    $insertOrderItemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $updateStockStmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE product_id = ?");
    foreach ($cart as $item) {
        // Insert order item
        $insertOrderItemStmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $insertOrderItemStmt->execute();

        // Update stock
        $updateStockStmt->bind_param("ii", $item['quantity'], $item['product_id']);
        $updateStockStmt->execute();
    }
    $insertOrderItemStmt->close();
    $updateStockStmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode(["success" => true, "order_id" => $order_id]);

} catch (Exception $e) {
    // Rollback transaction if anything fails
    $conn->rollback();

    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

?>
