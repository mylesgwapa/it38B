<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: user_login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
include 'db_connection.php';

$query = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
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
    }

    table th, table td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
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
  </style>
</head>
<body>

  <div class="container">
    <h2>📜 Order History</h2>

    <table>
      <tr>
        <th>Order ID</th>
        <th>Date</th>
        <th>Payment</th>
        <th>Delivery</th>
        <th>Preferred Time</th>
        <th>Status / Action</th>
      </tr>

      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['order_id'] ?></td>
          <td><?= $row['order_date'] ?></td>
          <td><?= $row['payment_mode'] ?></td>
          <td><?= $row['delivery_mode'] ?></td>
          <td><?= $row['preferred_time'] ?></td>
          <td>
            <?= $row['status'] ?>
            <?php if (strtolower($row['status']) === 'completed'): ?>
              <br>
              <a href="feedback_form.php?order_id=<?= $row['order_id'] ?>" class="feedback-btn">Give Feedback</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6">You have no orders yet.</td></tr>
      <?php endif; ?>
    </table>

    <a href="products.php" class="back-btn">← Back to Products</a>
  </div>

</body>
</html>
