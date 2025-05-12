<?php
// Start the session
session_start();

// Include database connection file
include('db_connection.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username and password are provided
    if (!empty($username) && !empty($password)) {
        // Query to fetch the user from the database
        $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($conn, $query);

        // Check if the user exists
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start the session and store user data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                // Redirect to the dashboard or home page
                header("Location: dashboard.php");
                exit();
            } else {
                // Invalid password
                $_SESSION['login_error'] = "Incorrect password. Please try again.";
                header("Location: index.php");
                exit();
            }
        } else {
            // Username does not exist
            $_SESSION['login_error'] = "Username not found. Please try again.";
            header("Location: index.php");
            exit();
        }
    } else {
        // Username or password is empty
        $_SESSION['login_error'] = "Please enter both username and password.";
        header("Location: index.php");
        exit();
    }
}
