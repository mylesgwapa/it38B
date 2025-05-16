<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'])) {
    $feedback_id = intval($_POST['feedback_id']);
    $customer_id = $_SESSION['customer_id'];

    // Mark as read
    $stmt = $conn->prepare("UPDATE feedback SET reply_read = 1 WHERE feedback_id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $feedback_id, $customer_id);
    $stmt->execute();

    // Redirect URL - change as needed
    // Example: redirect to messages.php to view replies
    echo "messages.php";
} else {
    http_response_code(400);
    echo "Invalid request";
}
?>
