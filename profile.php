<?php
// Start the session to handle login check
session_start();

// Check if the user is logged in (i.e., check if the customer_id is set in the session)
if (!isset($_SESSION['customer_id'])) {
    header('Location: user_login.php');  // Redirect to login page if not logged in
    exit();
}

// Get the customer_id from the session
$customer_id = $_SESSION['customer_id'];

// Include the database connection file
require 'db_connection.php';

// Prepare the SQL query to fetch customer data based on customer_id
$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);  // Bind the parameter as an integer (i)
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();

// Check if the customer exists
if ($result->num_rows > 0) {
    // Fetch customer data as an associative array
    $customer = $result->fetch_assoc();
} else {
    // If customer not found, display an error message
    echo "No customer found with the given ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="profile-container">
            <h2>Welcome, <?php echo htmlspecialchars($customer['customername']); ?>!</h2>  <!-- Display customer's name -->

            <!-- Display customer details -->
            <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($customer['address']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($customer['phone_number']); ?></p>

            <a href="update_profile.php">Edit Profile</a> <!-- Link to edit profile -->
            <a href="logout.php">Logout</a> <!-- Logout link -->
        </div>
    </div>
</body>
</html>
