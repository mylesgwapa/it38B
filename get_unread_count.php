<?php
include("db_connection.php");
$result = mysqli_query($conn, "SELECT COUNT(*) AS unread FROM feedback WHERE is_read = 0");
$data = mysqli_fetch_assoc($result);
echo json_encode(['unread' => $data['unread']]);
?>
