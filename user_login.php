<?php
// Start the session to handle error/success messages
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>STORESYNC</h2>

            <!-- Display Error Message -->
            <?php if (isset($_SESSION['login_error'])) : ?>
                <div class="error-message" style="color: red; margin-bottom: 10px;">
                    <?php echo htmlspecialchars($_SESSION['login_error']); ?>
                    <?php unset($_SESSION['login_error']); // Clear error message after displaying ?>
                </div>
            <?php endif; ?>

            <form action="customer-login-action.php" method="POST">
                <div class="input-group">
                    <label for="customername">Customer Name</label> <!-- Changed from username to customername -->
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="customername" name="customername" required> <!-- Changed from username to customername -->
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>

                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="user_signup.php">Sign Up</a></p> <!-- Corrected link -->
        </div>
    </div>
</body>
</html>
