<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include 'db_connection.php';

// Handle admin reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'], $_POST['admin_reply'])) {
    $feedback_id = intval($_POST['feedback_id']);
    $admin_reply = mysqli_real_escape_string($conn, $_POST['admin_reply']);

    // Update the feedback table with admin's reply
    $update = "UPDATE feedback SET reply = '$admin_reply' WHERE feedback_id = $feedback_id";
    mysqli_query($conn, $update);
}

// Query to fetch feedback along with customer name
$query = "SELECT f.feedback_id, f.feedback, f.rating, c.customername, f.reply, f.customer_reply, f.created_at 
          FROM feedback f 
          JOIN customers c ON f.customer_id = c.customer_id 
          ORDER BY f.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Customer Feedback</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .feedback-box {
      border: 1px solid #ccc;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 5px;
      background: #fff;
    }
    .admin-reply {
      margin-top: 10px;
      padding: 10px;
      background: #e8f0ff;
      border-left: 4px solid #0056b3;
    }
    .customer-reply {
      margin-top: 10px;
      padding: 10px;
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
  <h2>Customer Feedback & Replies</h2>

  <?php
  // Check if there are any feedback entries
  if (mysqli_num_rows($result) > 0) {
      // Loop through feedback records
      while ($row = mysqli_fetch_assoc($result)) {
          echo "<div class='feedback-box'>";
          echo "<p><strong>Customer:</strong> " . htmlspecialchars($row['customername']) . "</p>";
          echo "<p><strong>Feedback:</strong> " . htmlspecialchars($row['feedback']) . "</p>";
          echo "<p><strong>Rating:</strong> " . htmlspecialchars($row['rating']) . " ⭐</p>";
          echo "<p><small>Submitted on: " . htmlspecialchars($row['created_at']) . "</small></p>";

          // Display admin's reply
          if (!empty($row['reply'])) {
              echo "<div class='admin-reply'><strong>Admin's Reply:</strong> " . htmlspecialchars($row['reply']) . "</div>";
          } else {
              echo "<p><em>No reply from admin yet.</em></p>";
              // Admin reply form
              echo "<form method='POST' action=''>";
              echo "<textarea name='admin_reply' placeholder='Write your reply here...' required></textarea>";
              echo "<input type='hidden' name='feedback_id' value='" . $row['feedback_id'] . "'>";
              echo "<button type='submit'>Send Reply</button>";
              echo "</form>";
          }

          // Display customer's name instead of "Customer Reply"
          if (!empty($row['customer_reply'])) {
              echo "<div class='customer-reply'><strong>" . htmlspecialchars($row['customername']) . " replied:</strong> " . htmlspecialchars($row['customer_reply']) . "</div>";
          } else {
              echo "<p><em>No reply from customer yet.</em></p>";
          }

          // Allow admin to reply even if the customer has already replied
          echo "<form method='POST' action=''>";
          echo "<textarea name='admin_reply' placeholder='Admin, write your reply here...' required></textarea>";
          echo "<input type='hidden' name='feedback_id' value='" . $row['feedback_id'] . "'>";
          echo "<button type='submit'>Reply Again</button>";
          echo "</form>";

          echo "</div>";
      }
  } else {
      echo "<p>No feedback submitted yet.</p>";
  }
  ?>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
  