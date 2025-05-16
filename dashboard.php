<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('db_connection.php');

// Get total users count (Employees)
$query = "SELECT COUNT(*) AS total_users FROM users";
$result = mysqli_query($conn, $query);
$user_count = mysqli_fetch_assoc($result)['total_users'];

// Get total customers count
$customer_query = "SELECT COUNT(*) AS total_customers FROM customers";
$customer_result = mysqli_query($conn, $customer_query);
$customer_count = mysqli_fetch_assoc($customer_result)['total_customers'];

// Fetch customer feedback (activity data)
$feedback_query = "SELECT f.feedback_id, f.feedback AS comment, f.rating, c.customername, f.created_at 
                   FROM feedback f 
                   JOIN customers c ON f.customer_id = c.customer_id
                   ORDER BY f.created_at DESC LIMIT 5";
$feedback_result = mysqli_query($conn, $feedback_query);
$feedbacks = mysqli_fetch_all($feedback_result, MYSQLI_ASSOC);

// Handle feedback delete action
if (isset($_GET['delete_feedback_id'])) {
    $feedback_id = $_GET['delete_feedback_id'];
    $delete_query = "DELETE FROM feedback WHERE feedback_id = $feedback_id";
    mysqli_query($conn, $delete_query);
    header("Location: dashboard.php"); // Redirect after delete
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STORESYNC Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <!-- Font Awesome CDN for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Table Styling */
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
  }
  
  body {
    background: #e1e1e1;
  }
  
  .container {
    display: flex;
    height: 100vh;
  }
  
  .sidebar {
    background:rgba(92, 91, 91, 0.72);
    width: 350px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  
  .sidebar h2 {
    margin-bottom: -70px; 
    text-align: center;
    font-size: 24px;
      color: white;
  }
  
  .sidebar nav {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  
  .sidebar nav button {
    background:rgba(167, 162, 162, 0.91);
    border: none;
    padding: 15px 19px;
    text-align: left;
    cursor: pointer;
    font-size: 16px;
    border-radius: 40px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: background 0.3s;
  }
  
  .sidebar nav button:hover {
    background:rgb(214, 205, 205);
  }
.logout {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.sidebar .logout button {
    display: flex;
    align-items: center;
    gap: 10px;
    border: none;
    padding: 12px 20px;
    font-size: 16px;
      font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
    background: transparent;  /* No background */
    color: inherit;            /* Inherit text color */
}

.sidebar .logout button:hover {
    text-decoration: underline; /* Optional hover effect */
}




        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: space-evenly;
        }
        .action-buttons button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .action-buttons button.delete {
            background-color: #e74c3c;
        }
        .action-buttons button.reply {
            background-color: #2ecc71;
        }
    </style>
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
    <button onclick="location.href='logout.php'">
        <i class="fas fa-sign-out-alt"></i>
        <span>LOG OUT</span>
    </button>
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
                    <p>NO. OF EMPLOYEES</p>
                </div>
                <div class="card">
                    <i class="fas fa-users card-icon"></i>
                    <h3><?php echo $customer_count; ?></h3>
                    <p>NO. OF CUSTOMERS</p>
                </div>
                <div class="card">
                    <i class="fas fa-chart-bar card-icon"></i>
                    <h3>Analytics</h3>
                </div>
            </div>
        </div>

      <div class="activity">
    <h3>ACTIVITY</h3>
    <?php if (empty($feedbacks)) : ?>
        <p>No feedback yet</p>
    <?php else : ?>
        <!-- Feedback Table -->
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Comments</th>
                    <th>Rating</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($feedback['customername']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['comment']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
                        <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

    </div>
</div>

</body>
</html>
