<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['customer_id']) || !isset($_POST['feedback_id'])) {
    header('Location: products.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$feedback_id = intval($_POST['feedback_id']);

// Delete the reply (set reply to NULL and reset read status) or delete entire feedback row depending on your logic
// If you want to keep feedback but remove reply and notification:
$sql = "UPDATE feedback SET reply = NULL, reply_read = 0 WHERE feedback_id = ? AND customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $feedback_id, $customer_id);
$stmt->execute();

// Alternatively, if you want to delete the entire feedback record:
// $sql = "DELETE FROM feedback WHERE feedback_id = ? AND customer_id = ?";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param("ii", $feedback_id, $customer_id);
// $stmt->execute();

header('Location: products.php');
exit();
?>
