<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    // First delete from order_items to maintain referential integrity
    $conn->query("DELETE FROM order_items WHERE order_id = $order_id");

    // Then delete from orders
    $conn->query("DELETE FROM orders WHERE order_id = $order_id");
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>
