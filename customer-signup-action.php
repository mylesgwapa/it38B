<?php
// Start the session to handle error/success messages
session_start();

// Database connection (make sure to replace with your actual DB credentials)
require 'db_connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get form data and sanitize
    $customername = trim($_POST['username']);
    $email = trim($_POST['email']);
    $address = trim($_POST['position']);
    $phone_number = trim($_POST['department']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']);

    // Validation checks
    if (empty($customername) || empty($email) || empty($address) || empty($phone_number) || empty($password) || empty($confirm_password)) {
        $_SESSION['signup_error'] = "All fields are required!";
        header('Location: user_signup.php');
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['signup_error'] = "Passwords do not match!";
        header('Location: user_signup.php');
        exit();
    }

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to check if email already exists
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['signup_error'] = "Email is already taken!";
        header('Location: user_signup.php');
        exit();
    }

    // Prepare SQL statement to insert the new customer
    $stmt = $conn->prepare("INSERT INTO customers (customername, email, address, phone_number, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $customername, $email, $address, $phone_number, $hashed_password);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['signup_success'] = "Sign up successful! Please log in.";
        header('Location: user_login.php');
        exit();
    } else {
        $_SESSION['signup_error'] = "An error occurred. Please try again later.";
        header('Location: user_signup.php');
        exit();
    }
}
?>
