<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['customer_id']) || !isset($_POST['feedback_id'])) {
    header('Location: products.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$feedback_id = intval($_POST['feedback_id']);

// Update reply_read = 1 (mark as read) only for this customer
$sql = "UPDATE feedback SET reply_read = 1 WHERE feedback_id = ? AND customer_id = ? AND reply IS NOT NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $feedback_id, $customer_id);
$stmt->execute();

header('Location: products.php');
exit();
?>
