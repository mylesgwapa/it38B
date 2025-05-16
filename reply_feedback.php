<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db_connection.php');

// Fetch feedback details based on feedback_id
if (isset($_GET['feedback_id'])) {
    $feedback_id = $_GET['feedback_id'];
    $query = "SELECT f.feedback_id, f.feedback AS comment, f.rating, c.customername, f.created_at, f.reply 
              FROM feedback f 
              JOIN customers c ON f.customer_id = c.customer_id 
              WHERE f.feedback_id = $feedback_id";
    $result = mysqli_query($conn, $query);
    $feedback = mysqli_fetch_assoc($result);
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reply = mysqli_real_escape_string($conn, $_POST['reply']);
    
    // Update the feedback with the admin's reply
    $update_query = "UPDATE feedback SET reply = '$reply' WHERE feedback_id = $feedback_id";
    mysqli_query($conn, $update_query);
    
    // Redirect back to the dashboard after submitting the reply
    header("Location: dashboard.php");
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Feedback</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<div class="container">
    <aside class="sidebar">
        <h2>DASHBOARD</h2>
        <nav>
            <button onclick="location.href='employee.php'"><i class="fas fa-user-tie"></i> EMPLOYEE</button>
            <button onclick="location.href='pos.php'"><i class="fas fa-cash-register"></i> POS INTEGRATION</button>
            <button onclick="location.href='customer.php'"><i class="fas fa-users"></i> CUSTOMER</button>
            <button onclick="location.href='sales.php'"><i class="fas fa-shopping-cart"></i> SALES</button>
        </nav>
        <div class="logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>LOG OUT</span>
        </div>
    </aside>

    <div class="main-content">
        <h2>Reply to Feedback</h2>

        <?php if ($feedback) : ?>
            <div class="feedback-details">
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($feedback['customername']); ?></p>
                <p><strong>Comment:</strong> <?php echo htmlspecialchars($feedback['comment']); ?></p>
                <p><strong>Rating:</strong> <?php echo htmlspecialchars($feedback['rating']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($feedback['created_at']); ?></p>
                <p><strong>Previous Reply:</strong> <?php echo htmlspecialchars($feedback['reply']) ?: "No reply yet."; ?></p>
            </div>

            <form method="POST">
                <label for="reply">Your Reply:</label>
                <textarea name="reply" id="reply" rows="5" required><?php echo htmlspecialchars($feedback['reply']); ?></textarea>
                <br>
                <button type="submit">Submit Reply</button>
            </form>
        <?php else : ?>
            <p>Feedback not found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
