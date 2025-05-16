<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    http_response_code(403);
    echo 'user_login.php';
    exit();
}

if (!isset($_POST['feedback_id'])) {
    http_response_code(400);
    echo 'products.php';
    exit();
}

$customer_id = $_SESSION['customer_id'];
$feedback_id = intval($_POST['feedback_id']);

include 'db_connection.php';

// Update the reply_read to 1 only if it belongs to this customer and is unread
$stmt = $conn->prepare("UPDATE feedback SET reply_read = 1 WHERE feedback_id = ? AND customer_id = ? AND reply_read = 0");
$stmt->bind_param("ii", $feedback_id, $customer_id);
$stmt->execute();

$stmt->close();
$conn->close();

// Return URL to redirect after marking read
echo 'messages.php?feedback_id=' . $feedback_id;
?>
