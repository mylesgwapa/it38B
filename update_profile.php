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

// Update profile process
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $customername = trim($_POST['customername']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone_number = trim($_POST['phone_number']);

    // Validation (You can add additional checks here)
    if (empty($customername) || empty($email) || empty($address) || empty($phone_number)) {
        echo "All fields are required!";
    } else {
        // Prepare the SQL query to update the customer's data
        $stmt = $conn->prepare("UPDATE customers SET customername = ?, email = ?, address = ?, phone_number = ? WHERE customer_id = ?");
        $stmt->bind_param("ssssi", $customername, $email, $address, $phone_number, $customer_id);

        // Execute the query
        if ($stmt->execute()) {
            echo "Profile updated successfully!";
        } else {
            echo "Error updating profile. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Update Your Profile</h2>

            <!-- Display customer data in form fields -->
            <form action="update_profile.php" method="POST">
                <div class="input-group">
                    <label for="customername">Customer Name</label>
                    <input type="text" id="customername" name="customername" value="<?php echo htmlspecialchars($customer['customername']); ?>" required>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                </div>

                <div class="input-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>" required>
                </div>

                <div class="input-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($customer['phone_number']); ?>" required>
                </div>

                <button type="submit">Update Profile</button>
            </form>

            <!-- Back to Dashboard Button -->
            <br><br>
            <a href="products.php">
                <button type="button">Back to Dashboard</button>
            </a>
        </div>
    </div>
</body>
</html>
