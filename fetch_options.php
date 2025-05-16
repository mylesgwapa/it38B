<?php
include 'db_connection.php';
$product_id = intval($_GET['product_id']);
$result = $conn->query("SELECT option_name, option_price FROM product_options WHERE product_id = $product_id");

$options = [];
while ($row = $result->fetch_assoc()) {
    $options[] = $row;
}
echo json_encode($options);
?>
