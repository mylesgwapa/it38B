<?php
// Start the session to handle error/success messages
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <img src="images/logo.png" alt="Logo" class="header-logo">
                <h2>Sign Up</h2>
            </div>

            <!-- Display Error Message -->
            <?php if (isset($_SESSION['signup_error'])) : ?>
                <div class="error-message" style="color: red; margin-bottom: 10px;">
                    <?php echo htmlspecialchars($_SESSION['signup_error']); ?>
                    <?php unset($_SESSION['signup_error']); // Clear error message after displaying ?>
                </div>
            <?php endif; ?>

            <!-- Display Success Message -->
            <?php if (isset($_SESSION['signup_success'])) : ?>
                <div class="success-message" style="color: green; margin-bottom: 10px;">
                    <?php echo htmlspecialchars($_SESSION['signup_success']); ?>
                    <?php unset($_SESSION['signup_success']); // Clear success message after displaying ?>
                </div>
            <?php endif; ?>

            <form action="customer-signup-action.php" method="POST">
                <div class="input-group">
                    <label for="username">Customer Name</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="position">Address</label>
                    <div class="input-icon">
                        <i class="fas fa-briefcase"></i>
                        <input type="text" id="position" name="position" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="department">Phone Number</label>
                    <div class="input-icon">
                        <i class="fas fa-building"></i>
                        <input type="text" id="department" name="department" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="confirm-password">Confirm Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm-password" name="confirm-password" required>
                    </div>
                </div>

                <button type="submit">Sign Up</button>
            </form>
            <p>Already have an account? <a href="user_login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
