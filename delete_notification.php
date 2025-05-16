<?php
session_start();
include 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'])) {
    $feedback_id = intval($_POST['feedback_id']);
    $customer_id = $_SESSION['customer_id'];

    // Delete reply for this feedback (set reply and reply_read to NULL/0)
    $stmt = $conn->prepare("UPDATE feedback SET reply = NULL, reply_read = 0 WHERE feedback_id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $feedback_id, $customer_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
