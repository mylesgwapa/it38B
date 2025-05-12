<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Set hourly rate
$hourly_rate = 100; // Adjust as needed

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch existing record
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();

    if (!$attendance) {
        echo "<script>alert('Record not found.'); window.location='summary.php';</script>";
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $time_in = $_POST['time_in'];
        $time_out = $_POST['time_out'];

        $start = new DateTime($time_in);
        $end = new DateTime($time_out);
        $interval = $start->diff($end);
        $hours_worked = $interval->h + ($interval->i / 60);
        if ($interval->d > 0) {
            $hours_worked += $interval->d * 24;
        }
        $total_pay = $hours_worked * $hourly_rate;

        $update = $conn->prepare("UPDATE attendance SET time_in = ?, time_out = ?, hours_worked = ?, total_pay = ? WHERE id = ?");
        $update->bind_param("ssddi", $time_in, $time_out, $hours_worked, $total_pay, $id);

        if ($update->execute()) {
            echo "<script>alert('Attendance updated successfully!'); window.location='summary.php';</script>";
        } else {
            echo "<script>alert('Error updating record.'); window.location='summary.php';</script>";
        }
        exit();
    }
} else {
    echo "<script>alert('Invalid request.'); window.location='summary.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Attendance</title>
    <link rel="stylesheet" href="inventory.css">
    <style>
        form {
            max-width: 400px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f9f9f9;
        }
        form input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        form button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Edit Attendance Record</h2>
    <form method="POST">
        <label for="time_in">Time In:</label>
        <input type="datetime-local" name="time_in" value="<?php echo date('Y-m-d\TH:i', strtotime($attendance['time_in'])); ?>" required>

        <label for="time_out">Time Out:</label>
        <input type="datetime-local" name="time_out" value="<?php echo date('Y-m-d\TH:i', strtotime($attendance['time_out'])); ?>" required>

        <button type="submit">Update Attendance</button>
    </form>

    <div style="text-align:center; margin-top:20px;">
        <a href="summary.php">← Back to Summary</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>
