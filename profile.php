<?php
// Start the session to handle login check
session_start();

// Check if the user is logged in (i.e., check if the customer_id is set in the session)
if (!isset($_SESSION['customer_id'])) {
    header('Location: user_login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
require 'db_connection.php';

$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();
} else {
    echo "No customer found with the given ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            font-family: Arial, sans-serif;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        table td.label {
            font-weight: bold;
            background-color: #f2f2f2;
            width: 30%;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        .actions {
            text-align: center;
        }
        .actions a {
            display: inline-block;
            padding: 10px 16px;
            margin: 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .actions a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($customer['customername']); ?>!</h2>

    <table>
        <tr>
            <td class="label">Email</td>
            <td><?php echo htmlspecialchars($customer['email']); ?></td>
        </tr>
        <tr>
            <td class="label">Address</td>
            <td><?php echo htmlspecialchars($customer['address']); ?></td>
        </tr>
        <tr>
            <td class="label">Phone Number</td>
            <td><?php echo htmlspecialchars($customer['phone_number']); ?></td>
        </tr>
    </table>

    <div class="actions">
        <a href="update_profile.php">Edit Profile</a>
        <a href="products.php">⬅ Back to Store</a>
        <a href="logout.php">Logout</a>
    </div>
</div>
</body>
</html>
