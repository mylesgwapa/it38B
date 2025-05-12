<?php
session_start();
include 'db_connection.php';  // your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customername = $_POST['customername'];
    $password = $_POST['password'];

    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT customer_id, password FROM customers WHERE customername = ?");
    $stmt->bind_param("s", $customername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($customer_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Login success: set session
            $_SESSION['customer_id'] = $customer_id;
            $_SESSION['customername'] = $customername;

            header('Location: products.php');
            exit();
        } else {
            // Wrong password
            $_SESSION['login_error'] = "Invalid password.";
            header('Location: user_login.php');
            exit();
        }
    } else {
        // Customer not found
        $_SESSION['login_error'] = "Customer not found.";
        header('Location: user_login.php');
        exit();
    }
}
?>
