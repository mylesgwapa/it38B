<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Set hourly rate
$hourly_rate = 100; // Adjust as needed

// Create 'attendance' table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    time_in DATETIME NOT NULL,
    time_out DATETIME NOT NULL,
    hours_worked DECIMAL(5,2) NOT NULL,
    total_pay DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

// Handle form submission for adding attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
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

    $stmt = $conn->prepare("INSERT INTO attendance (user_id, time_in, time_out, hours_worked, total_pay) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issdd", $user_id, $time_in, $time_out, $hours_worked, $total_pay);
    $stmt->execute();

    echo "<script>alert('Attendance recorded successfully!'); window.location='employee_summary.php';</script>";
    exit();
}

// Fetch employees for dropdown
$employees = mysqli_query($conn, "SELECT id, username FROM users");

// Fetch attendance records
$attendance = mysqli_query($conn, "SELECT a.id, u.username, a.time_in, a.time_out, a.hours_worked, a.total_pay FROM attendance a JOIN users u ON a.user_id = u.id ORDER BY a.time_in DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee Summary</title>
  <link rel="stylesheet" href="inventory.css">
  <style>
    table {
      width: 90%;
      margin: 20px auto;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: center;
    }
    th {
      background-color: #f4f4f4;
    }
    form {
      max-width: 400px;
      margin: 30px auto;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 6px;
      background-color: #f9f9f9;
    }
    form input, form select {
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
    .delete-btn {
      background-color: #dc3545;
    }
    .delete-btn:hover {
      background-color: #c82333;
    }
  </style>
</head>
<body>
  <h2 style="text-align:center;">Admin: Input Employee Attendance</h2>
  <form method="POST">
    <label for="user_id">Select Employee:</label>
    <select name="user_id" required>
      <option value="">-- Select Employee --</option>
      <?php while ($emp = mysqli_fetch_assoc($employees)): ?>
        <option value="<?php echo $emp['id']; ?>"><?php echo htmlspecialchars($emp['username']); ?></option>
      <?php endwhile; ?>
    </select>

    <label for="time_in">Time In:</label>
    <input type="datetime-local" name="time_in" required>

    <label for="time_out">Time Out:</label>
    <input type="datetime-local" name="time_out" required>

    <button type="submit">Submit Attendance</button>
  </form>



  <div style="text-align:center; margin-top:20px;">
    <a href="employee.php">← Back to Employee Management</a>
  </div>
</body>
</html>

<?php mysqli_close($conn); ?>
