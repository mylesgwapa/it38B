<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}
$customer_id = $_SESSION['customer_id'];

// Include database connection
include 'db_connection.php';

// Fetch unread admin replies for this customer (assuming there is a field to track read/unread)
$query = "SELECT reply_message, reply_date FROM admin_replies WHERE customer_id = ? AND is_read = 0 ORDER BY reply_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$replies = [];
while ($row = $result->fetch_assoc()) {
    $replies[] = [
        'reply_message' => $row['reply_message'],
        'reply_date' => $row['reply_date'],
    ];
}

echo json_encode(['success' => true, 'replies' => $replies]);
