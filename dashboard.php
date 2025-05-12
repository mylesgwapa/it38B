<?php
// Start the session
session_start();

// Check if the user is logged in, if not redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include('db_connection.php');

// Fetch the number of registered users from the database
$query = "SELECT COUNT(*) AS total_users FROM users";
$result = mysqli_query($conn, $query);
$user_count = mysqli_fetch_assoc($result)['total_users'];

// Optionally, fetch the latest activity (e.g., customer comments or recent activity)
$activity_query = "SELECT * FROM activities ORDER BY activity_date DESC LIMIT 5";  // Adjust according to your table
$activity_result = mysqli_query($conn, $activity_query);
$activities = mysqli_fetch_all($activity_result, MYSQLI_ASSOC);

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>STORESYNC</title>
  <link rel="stylesheet" href="dashboard.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="container">
  <aside class="sidebar">
    <h2>DASHBOARD</h2>
    <nav>
      <button onclick="location.href='inventory.html'"><i class="fas fa-warehouse"></i> INVENTORY</button>
      <button onclick="location.href='employee.php'"><i class="fas fa-user-tie"></i> EMPLOYEE</button>
      <button onclick="location.href='pos integration.html'"><i class="fas fa-cash-register"></i> POS INTEGRATION</button>
      <button onclick="location.href='customer.html'"><i class="fas fa-users"></i> CUSTOMER</button>
      <button onclick="location.href='sales.html'"><i class="fas fa-shopping-cart"></i> SALES</button>
    </nav>
    <div class="logout">
      <i class="fas fa-sign-out-alt"></i>
      <span>LOG OUT</span>
    </div>
  </aside>

  <div class="main-content">

    <div class="top-section">
      <div class="search">
        <input type="text" placeholder="Search...">
      </div>
    
      <div class="cards-container">
        <div class="card">
          <i class="fas fa-users card-icon"></i>
          <h3><?php echo $user_count; ?></h3>
          <p>NO. OF EMPLOYEE</p>
        </div>
        <div class="card">
          <i class="fas fa-chart-bar card-icon"></i>
          <h3>Analytics</h3>
        </div>
      </div>
    </div>
    
    <div class="activity">
      <h3>ACTIVITY</h3>
      <div class="activity-header">
        <span>CUSTOMER</span>
        <span>COMMENTS</span>
        <span>RATE</span>
      </div>
      <?php if (empty($activities)) : ?>
        <p>No activity yet</p>
      <?php else : ?>
        <?php foreach ($activities as $activity) : ?>
          <div class="activity-item">
            <span><?php echo htmlspecialchars($activity['customer_name']); ?></span>
            <span><?php echo htmlspecialchars($activity['comment']); ?></span>
            <span><?php echo htmlspecialchars($activity['rating']); ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</div>

</body>
</html>
