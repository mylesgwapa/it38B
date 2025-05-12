<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image_url = $_POST['image_url'];

    // Prepare the SQL INSERT statement
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, quantity, image) VALUES (?, ?, ?, ?, ?)");

    // Bind the parameters (s = string, d = double, i = integer)
    $stmt->bind_param("ssdis", $product_name, $description, $price, $quantity, $image_url);
    
    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Success - Redirect back or show success message
        echo "Product added successfully!";
    } else {
        // Error - Show error message
        echo "Error: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
