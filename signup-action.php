<?php
// Start session for error messages
session_start();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $position = trim($_POST['position']);
    $department = trim($_POST['department']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $_SESSION['signup_error'] = "Passwords do not match!";
        header("Location: signup.php");
        exit();
    }

    // Validate inputs
    if (empty($username) || empty($email) || empty($position) || empty($department) || empty($password)) {
        $_SESSION['signup_error'] = "All fields are required!";
        header("Location: signup.php");
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Database connection
    $host = 'localhost'; // your database host
    $db = 'final'; // your database name
    $user = 'root'; // your database username
    $pass = ''; // your database password

    // Create a new PDO connection
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the username or email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $_SESSION['signup_error'] = "Username or Email already exists!";
            header("Location: signup.php");
            exit();
        }

        // Insert user data into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, email, position, department, password) VALUES (:username, :email, :position, :department, :password)");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'position' => $position,
            'department' => $department,
            'password' => $hashedPassword
        ]);

        // Redirect to a success page or login page
        $_SESSION['signup_success'] = "Sign up successful! You can now log in.";
        header("Location: index.php"); // or redirect to the login page
        exit();

    } catch (PDOException $e) {
        // Handle database connection or query errors
        $_SESSION['signup_error'] = "Error: " . $e->getMessage();
        header("Location: signup.php");
        exit();
    }
}
?>
