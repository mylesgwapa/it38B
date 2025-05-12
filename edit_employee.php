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

<form method="POST">
    <input type="text" name="username" value="<?php echo $employee['username']; ?>" required>
    <input type="email" name="email" value="<?php echo $employee['email']; ?>" required>
    <input type="text" name="position" value="<?php echo $employee['position']; ?>" required>
    <input type="text" name="department" value="<?php echo $employee['department']; ?>" required>
    <button type="submit">Update</button>
</form>
