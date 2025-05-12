<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Fetch attendance records
$attendance = mysqli_query($conn, "
    SELECT a.id, u.username, a.time_in, a.time_out, a.hours_worked, a.total_pay 
    FROM attendance a 
    JOIN users u ON a.user_id = u.id 
    ORDER BY a.time_in DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Attendance Summary</title>
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
        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .edit-btn {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Employee Attendance Summary</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Hours Worked</th>
                <th>Total Pay</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($attendance)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo $row['time_in']; ?></td>
                <td><?php echo $row['time_out']; ?></td>
                <td><?php echo number_format($row['hours_worked'], 2); ?> hrs</td>
                <td>₱<?php echo number_format($row['total_pay'], 2); ?></td>
                <td>
                    <a href="edit_attendance.php?id=<?php echo $row['id']; ?>">
                        <button class="edit-btn">Edit</button>
                    </a>
                    <a href="delete_attendance.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                        <button class="delete-btn">Delete</button>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div style="text-align:center; margin-top:20px;">
        <a href="employee.php">← Back to Employee Management</a>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>
