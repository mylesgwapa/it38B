<?php
include 'db_connection.php';

$order_id = $_POST['order_id'];
$status = $_POST['status'];

$stmt = $conn->prepare("UPDATE orders SET status=? WHERE order_id=?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    header("Location: pos.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>
