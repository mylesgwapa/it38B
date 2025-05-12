<?php
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, position, department, password) VALUES ('$username', '$email', '$position', '$department', '$password')";
    if (mysqli_query($conn, $query)) {
        header("Location: employee.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
