<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: user_login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (!$order_id) {
    die("Order ID not specified.");
}

include 'db_connection.php';

// Get customer name (column name is `customername`)
$name_query = "SELECT customername FROM customers WHERE customer_id = ?";
$name_stmt = $conn->prepare($name_query);
$name_stmt->bind_param("i", $customer_id);
$name_stmt->execute();
$name_result = $name_stmt->get_result();
$customer = $name_result->fetch_assoc();
$customer_name = $customer['customername'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback = $_POST['feedback'];
    $rating = $_POST['rating'];

    // Insert into feedback table
    $query = "INSERT INTO feedback (customer_id, order_id, feedback, rating) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisi", $customer_id, $order_id, $feedback, $rating);
    $stmt->execute();

    // Insert into activities table for dashboard
    $activity_query = "INSERT INTO activities (customername, comment, rating, activity_date) VALUES (?, ?, ?, NOW())";
    $activity_stmt = $conn->prepare($activity_query);
    $activity_stmt->bind_param("ssi", $customer_name, $feedback, $rating);
    $activity_stmt->execute();

    echo "<script>alert('Thank you for your feedback!'); window.location.href='products.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Give Feedback</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      padding: 40px;
    }
    .container {
      max-width: 600px;
      background: white;
      margin: auto;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    h2 {
      margin-bottom: 20px;
    }
    textarea, select {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      padding: 10px 20px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 4px;
    }
    button:hover {
      background-color: #218838;
    }
    .back-btn {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #007BFF;
    }
    .back-btn:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Give Feedback for Order #<?= htmlspecialchars($order_id) ?></h2>
    <form method="post">
      <label for="feedback">Your Feedback:</label>
      <textarea name="feedback" rows="4" required></textarea>

      <label for="rating">Rating:</label>
      <select name="rating" required>
        <option value="">--Select Rating--</option>
        <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
        <option value="4">⭐⭐⭐⭐ Good</option>
        <option value="3">⭐⭐⭐ Average</option>
        <option value="2">⭐⭐ Poor</option>
        <option value="1">⭐ Very Poor</option>
      </select>

      <button type="submit">Submit Feedback</button>
    </form>
    <a href="customer_purchase.php" class="back-btn">← Back to Order History</a>
  </div>
</body>
</html>
