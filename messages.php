<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: user_login.php");
    exit();
}

include 'db_connection.php';
$customer_id = $_SESSION['customer_id'];

// Handle customer reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'], $_POST['customer_reply'])) {
    $feedback_id = intval($_POST['feedback_id']);
    $customer_reply = mysqli_real_escape_string($conn, $_POST['customer_reply']);

    // Insert new customer reply into the feedback table
    $insert_reply = "UPDATE feedback SET customer_reply = '$customer_reply', status='unread', notification=0 WHERE feedback_id = $feedback_id AND customer_id = $customer_id";
    mysqli_query($conn, $insert_reply);
}

// Get customer name and all feedbacks with replies
$query = "SELECT f.feedback_id, f.feedback, f.rating, f.reply, f.customer_reply, f.created_at, c.customername
          FROM feedback f
          JOIN customers c ON f.customer_id = c.customer_id
          WHERE f.customer_id = $customer_id
          ORDER BY f.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Messages / Feedback Replies</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    .feedback-box {
      border: 1px solid #ccc;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 5px;
      background: #f9f9f9;
    }
    .admin-reply, .customer-reply {
      margin-top: 10px;
      padding: 10px;
      background: #e8f0ff;
      border-left: 4px solid #0056b3;
    }
    .customer-reply {
      background: #fef9e7;
      border-left: 4px solid #ffb400;
    }
    textarea {
      width: 100%;
      padding: 8px;
      margin-top: 8px;
    }
    button {
      margin-top: 6px;
      padding: 8px 15px;
    }
  </style>
</head>
<body>
  <h2>Your Feedback & Admin Replies</h2>

  <?php
  if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
          echo "<div class='feedback-box'>";
          echo "<p><strong>Your Feedback:</strong> " . htmlspecialchars($row['feedback']) . "</p>";
          echo "<p><strong>Rating:</strong> " . htmlspecialchars($row['rating']) . " ⭐</p>";
          echo "<p><small>Submitted on: " . htmlspecialchars($row['created_at']) . "</small></p>";

          // Show Admin's Reply (if any)
          if (!empty($row['reply'])) {
              echo "<div class='admin-reply'><strong>Admin Reply:</strong> " . htmlspecialchars($row['reply']) . "</div>";
          }

          // Show Customer's Reply (if any)
          if (!empty($row['customer_reply'])) {
              echo "<div class='customer-reply'><strong>" . htmlspecialchars($row['customername']) . " replied:</strong> " . htmlspecialchars($row['customer_reply']) . "</div>";
          }

          // Allow Customer to Reply at any time (even if admin hasn't replied yet)
          echo "<form method='POST' action=''>";
          echo "<textarea name='customer_reply' placeholder='Write your reply here...'></textarea>";
          echo "<input type='hidden' name='feedback_id' value='" . $row['feedback_id'] . "'>";
          echo "<button type='submit'>Send Customer Reply</button>";
          echo "</form>";

          echo "</div>";
      }
  } else {
      echo "<p>You haven't submitted any feedback yet.</p>";
  }
  ?>
</body>
</html>
