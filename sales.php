<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Report</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; }
    h1, h2 { color: #333; }
    form { margin-bottom: 20px; }
    select, input, button { padding: 10px; margin-right: 10px; }
    canvas { background: #fff; padding: 10px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    table { width: 100%; border-collapse: collapse; background: white; margin-top: 30px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background: #555; color: white; }
    .back-btn { background-color:rgb(76, 101, 175); color: white; padding: 10px 15px; border: none; cursor: pointer; }
    .back-btn:hover { background-color:rgb(69, 136, 160); }
  </style>
</head>
<body>

<h1>Sales Report</h1>

<form method="GET" id="filterForm">
  <label for="filter">View by:</label>
  <select name="filter" id="filter" onchange="toggleDateInput()">
    <option value="month" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'month') ? 'selected' : ''; ?>>Monthly</option>
    <option value="year" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'year') ? 'selected' : ''; ?>>Yearly</option>
  </select>

  <input type="month" name="month" id="monthInput" value="<?php echo $_GET['month'] ?? ''; ?>" style="display:none;">
  <input type="number" name="year" id="yearInput" min="2000" max="2099" placeholder="Enter year" value="<?php echo $_GET['year'] ?? ''; ?>" style="display:none;">

  <button type="submit">Generate</button>
  <button type="button" onclick="window.location.href='pos.php'" class="back-btn">← Back to Admin</button>
</form>

<script>
function toggleDateInput() {
  const filter = document.getElementById("filter").value;
  document.getElementById("monthInput").style.display = (filter === "month") ? "inline-block" : "none";
  document.getElementById("yearInput").style.display = (filter === "year") ? "inline-block" : "none";
}
window.onload = toggleDateInput;
</script>

<?php
$filter = $_GET['filter'] ?? 'month';
$periods = [];
$sales = [];

if ($filter === 'year' && !empty($_GET['year'])) {
    $year = intval($_GET['year']);
    $query = "SELECT MONTH(order_date) AS period, SUM(total_amount) AS total_sales
              FROM orders
              WHERE status = 'Completed' AND YEAR(order_date) = $year
              GROUP BY MONTH(order_date)
              ORDER BY period ASC";
    $label = "Month";
} elseif ($filter === 'month' && !empty($_GET['month'])) {
    $month = $_GET['month'];
    $query = "SELECT DAY(order_date) AS period, SUM(total_amount) AS total_sales
              FROM orders
              WHERE status = 'Completed' AND DATE_FORMAT(order_date, '%Y-%m') = '$month'
              GROUP BY DAY(order_date)
              ORDER BY period ASC";
    $label = "Day";
} else {
    $query = "SELECT DATE_FORMAT(order_date, '%Y-%m') AS period, SUM(total_amount) AS total_sales
              FROM orders
              WHERE status = 'Completed'
              GROUP BY DATE_FORMAT(order_date, '%Y-%m')
              ORDER BY period ASC";
    $label = "Month";
}

$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $periods[] = $row['period'];
    $sales[] = $row['total_sales'];
}

// Reset result for table rendering
$result = $conn->query($query);

$topProductsQuery = "SELECT p.name AS product_name, 
                            SUM(oi.quantity) AS total_sold, 
                            SUM(oi.quantity * oi.price) AS total_revenue
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.product_id
                     JOIN orders o ON oi.order_id = o.order_id
                     WHERE o.status = 'Completed'
                     GROUP BY oi.product_id
                     ORDER BY total_sold DESC
                     LIMIT 5";


$topProductsResult = $conn->query($topProductsQuery);
?>

<canvas id="salesChart" height="100"></canvas>

<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($periods); ?>,
      datasets: [{
        label: 'Sales (₱)',
        data: <?php echo json_encode($sales); ?>,
        backgroundColor: '#4CAF50',
        borderRadius: 5,
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: value => '₱' + value.toLocaleString()
          }
        }
      },
      plugins: {
        legend: { display: false },
        title: {
          display: true,
          text: 'Sales Report',
          font: { size: 18 }
        }
      }
    }
  });
</script>

<!-- Sales Table -->
<table>
  <thead>
    <tr>
      <th><?php echo $label; ?></th>
      <th>Total Sales (₱)</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($row['period']); ?></td>
      <td>₱<?php echo number_format($row['total_sales'], 2); ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<!-- Top Selling Products Table -->
<h2>Top Selling Products</h2>
<table>
  <thead>
    <tr>
      <th>Product Name</th>
      <th>Quantity Sold</th>
      <th>Total Revenue (₱)</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $topProductsResult->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($row['product_name']); ?></td>
      <td><?php echo $row['total_sold']; ?></td>
      <td>₱<?php echo number_format($row['total_revenue'], 2); ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
