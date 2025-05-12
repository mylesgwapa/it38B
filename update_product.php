<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image_url = $_POST['image_url'];

    if (!empty($product_id)) {
        // Update existing product
        $sql = "UPDATE products SET 
                    name = ?, 
                    description = ?, 
                    price = ?, 
                    quantity = ?, 
                    image = ? 
                WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdisi", $product_name, $description, $price, $quantity, $image_url, $product_id);
        if ($stmt->execute()) {
            echo "<script>alert('Product updated successfully!'); window.location.href='pos.php';</script>";
        } else {
            echo "<script>alert('Error updating product: " . $conn->error . "'); window.history.back();</script>";
        }
    } else {
        // Add new product
        $sql = "INSERT INTO products (name, description, price, quantity, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdiss", $product_name, $description, $price, $quantity, $image_url);
        if ($stmt->execute()) {
            echo "<script>alert('Product added successfully!'); window.location.href='pos.php';</script>";
        } else {
            echo "<script>alert('Error adding product: " . $conn->error . "'); window.history.back();</script>";
        }
    }
}
?>
