<?php
include("db_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'])) {
    $id = intval($_POST['feedback_id']);
    $update = "UPDATE feedback SET status = 'read' WHERE feedback_id = $id";
    echo mysqli_query($conn, $update) ? 'success' : 'error';
}
?>
