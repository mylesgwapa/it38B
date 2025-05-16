<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['product_id'])) {
        $_SESSION['error'] = "Invalid product ID.";
        header("Location: products.php");
        exit;
    }

    $product_id = intval($_POST['product_id']);

    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    try {
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Product deleted successfully.";
        } else {
            $_SESSION['error'] = "Product not found or already deleted.";
        }
    } catch (mysqli_sql_exception $e) {
        // Check if error is due to foreign key constraint
        if ($conn->errno == 1451) {
            $_SESSION['error'] = "Cannot delete product because it is linked to existing orders.";
        } else {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
        }
    }

    $stmt->close();
    $conn->close();

    header("Location: pos.php");
    exit;
} else {
    header("Location: pos.php");
    exit;
}
