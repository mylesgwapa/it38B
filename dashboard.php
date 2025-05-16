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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Table Styling */
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
