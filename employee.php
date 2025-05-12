<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM users WHERE id = $delete_id");
    header("Location: employee.php");
    exit();
}

// Fetch employees
$query = "SELECT id, username, email, position, department FROM users";
$result = mysqli_query($conn, $query);
$employee_count = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Management</title>
  <link rel="stylesheet" href="inventory.css"> 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
  <style>
    .employee-section {
      padding: 20px;
    }
    .employee-section table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    .employee-section th, .employee-section td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    .employee-section th {
      background-color: #f4f4f4;
    }
    .employee-section tr:hover {
      background-color: #f1f1f1;
    }
    .employee-actions {
      margin-top: 10px;
    }
    .employee-actions button {
      padding: 8px 12px;
      margin-right: 5px;
      background-color: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 4px;
    }
    .employee-actions button:hover {
      background-color: #0056b3;
    }
    .search input {
      padding: 8px;
      width: 250px;
      margin-bottom: 10px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    #addForm {
      margin-top: 20px;
      background-color: #f9f9f9;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      width: 400px;
    }
    #addForm input {
      width: 100%;
      padding: 8px;
      margin: 5px 0;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    #addForm button {
      margin-top: 10px;
      padding: 8px 12px;
      border: none;
      border-radius: 4px;
      background-color: #28a745;
      color: white;
      cursor: pointer;
    }
    #addForm button.cancel {
      background-color: #dc3545;
    }
    #addForm button:hover {
      opacity: 0.9;
    }
  </style>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <h2>DASHBOARD</h2>
      <nav>
        
        <button onclick="location.href='employee.php'"><i class="fas fa-user-tie"></i> EMPLOYEE</button>
        <button onclick="location.href='employee_summary.php'"><i class="fas fa-cash-register"></i> WORK HOURS</button>
        <button onclick="location.href='summary.php'"><i class="fas fa-cash-register"></i> REVIEWS</button>
      </nav>
      <div class="logout">
        <i class="fas fa-sign-out-alt"></i>
        <span><a href="logout.php">LOG OUT</a></span>
      </div>
    </aside>

    <div class="main-content">
      <div class="top-section">
        <div class="search">
          <input type="text" placeholder="Search Employee..." onkeyup="searchTable()">
        </div>
      </div>

      <div class="employee-section">
        <h3>Employee Management (Total: <?php echo $employee_count; ?>)</h3>
        <div class="employee-actions">
          <button onclick="document.getElementById('addForm').style.display='block'">Add New Employee</button>
        </div>

        <table id="employeeTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Email</th>
              <th>Position</th>
              <th>Department</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($employee_count > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?php echo $row['id']; ?></td>
                  <td><?php echo htmlspecialchars($row['username']); ?></td>
                  <td><?php echo htmlspecialchars($row['email']); ?></td>
                  <td><?php echo htmlspecialchars($row['position']); ?></td>
                  <td><?php echo htmlspecialchars($row['department']); ?></td>
                  <td>
                    <a href="edit_employee.php?id=<?php echo $row['id']; ?>"><button>Edit</button></a>
                    <a href="employee.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this employee?');"><button class="cancel">Delete</button></a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6">No employees found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Add Employee Form -->
      <div id="addForm" style="display:none;">
        <h3>Add New Employee</h3>
        <form action="add_employee.php" method="POST">
          <input type="text" name="username" placeholder="Username" required>
          <input type="email" name="email" placeholder="Email" required>
          <input type="text" name="position" placeholder="Position" required>
          <input type="text" name="department" placeholder="Department" required>
          <input type="password" name="password" placeholder="Password" required>
          <button type="submit">Add Employee</button>
          <button type="button" class="cancel" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
        </form>
      </div>

    </div>
  </div>

  <script>
    function searchTable() {
      const input = document.querySelector('.search input').value.toLowerCase();
      const rows = document.querySelectorAll('#employeeTable tbody tr');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
      });
    }
  </script>

</body>
</html>

<?php mysqli_close($conn); ?>
