<?php
include 'db_connection.php';

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Prepare the SQL DELETE statement
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        // Redirect to inventory page after deletion
        header("Location: pos.php#inventory");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo "No product ID specified.";
}

$conn->close();
?>
