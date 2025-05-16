<?php
include('db_connection.php');

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
$employee = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $department = $_POST['department'];

    $query = "UPDATE users SET username='$username', email='$email', position='$position', department='$department' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        header("Location: employee.php");
        exit();
    } else {
        echo "Error updating: " . mysqli_error($conn);
    }
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-image: url('images/a.png'); /* Replace with your actual background image path */
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: flex-start; /* Form on the left */
        align-items: center;
        padding-left: 60px; /* Padding on the left for spacing */
    }

    form {
        background: rgba(255, 255, 255, 0.45);
        padding: 30px 40px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        width: 350px;
        backdrop-filter: blur(6px);
    }

    form input {
        width: 100%;
        padding: 10px 12px;
        margin: 12px 0;
        border: 1.5px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        transition: border-color 0.3s;
    }

    form input:focus {
        border-color: #007BFF;
        outline: none;
    }

    form button {
        width: 100%;
        padding: 12px;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    form button:hover {
        background-color: #0056b3;
    }
</style>

<form method="POST">
    <input type="text" name="username" value="<?php echo htmlspecialchars($employee['username']); ?>" required>
    <input type="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
    <input type="text" name="position" value="<?php echo htmlspecialchars($employee['position']); ?>" required>
    <input type="text" name="department" value="<?php echo htmlspecialchars($employee['department']); ?>" required>
    <button type="submit">Update</button>
</form>
