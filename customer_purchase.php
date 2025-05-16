<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: user_login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
include 'db_connection.php';

// Fetch orders
$query = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Order History</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 1000px;
      margin: auto;
      background: white;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border-radius: 8px;
    }

    h2 {
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    table th, table td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
      vertical-align: top;
    }

    table th {
      background-color: #f0f0f0;
    }

    .back-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #007BFF;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }

    .back-btn:hover {
      background-color: #0056b3;
    }

    .feedback-btn {
      display: inline-block;
      margin-top: 8px;
      padding: 6px 12px;
      background-color: #ffc107;
      color: #000;
      text-decoration: none;
      border-radius: 4px;
      font-size: 14px;
    }

    .feedback-btn:hover {
      background-color: #e0a800;
    }

    /* Nested products table styles */
    .products-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    .products-table th, .products-table td {
      border: 1px solid #ccc;
      padding: 6px;
      text-align: left;
    }
    .products-table th {
      background-color: #e9ecef;
      text-align: left;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>📜 Order History</h2>

    <?php if ($orders_result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Products</th>
            <th>Date</th>
            <th>Payment</th>
            <th>Delivery</th>
            <th>Preferred Time</th>
            <th>Total</th>
            <th>Status / Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($order = $orders_result->fetch_assoc()): ?>
            <?php
              // Fetch products for this order
              $order_id = $order['order_id'];
              $products_sql = "SELECT p.name, oi.quantity, oi.price 
                               FROM order_items oi 
                               JOIN products p ON oi.product_id = p.product_id 
                               WHERE oi.order_id = ?";
              $stmt2 = $conn->prepare($products_sql);
              $stmt2->bind_param("i", $order_id);
              $stmt2->execute();
              $products_result = $stmt2->get_result();

              $calculated_total = 0;
              $product_details = '<table class="products-table">';
              $product_details .= '<thead><tr><th>Product Name</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr></thead><tbody>';

              while ($product = $products_result->fetch_assoc()) {
                  $subtotal = $product['quantity'] * $product['price'];
                  $calculated_total += $subtotal;
                  $product_details .= "<tr>
                                        <td>" . htmlspecialchars($product['name']) . "</td>
                                        <td>" . $product['quantity'] . "</td>
                                        <td>₱" . number_format($product['price'], 2) . "</td>
                                        <td>₱" . number_format($subtotal, 2) . "</td>
                                      </tr>";
              }
              $product_details .= '</tbody></table>';
              $stmt2->close();
            ?>
            <tr>
              <td><?= htmlspecialchars($order['order_id']) ?></td>
              <td><?= $product_details ?></td>
              <td><?= htmlspecialchars($order['order_date']) ?></td>
              <td><?= htmlspecialchars($order['payment_mode']) ?></td>
              <td><?= htmlspecialchars($order['delivery_mode']) ?></td>
              <td><?= htmlspecialchars($order['preferred_time']) ?></td>
              <td style="text-align: right;">₱<?= number_format($calculated_total, 2) ?></td>
              <td>
                <?= htmlspecialchars($order['status']) ?>
                <?php if (strtolower($order['status']) === 'completed'): ?>
                  <br>
                  <a href="feedback_form.php?order_id=<?= $order['order_id'] ?>" class="feedback-btn">Give Feedback</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>You have no orders yet.</p>
    <?php endif; ?>

    <a href="products.php" class="back-btn">← Back to Products</a>
  </div>

</body>
</html>
