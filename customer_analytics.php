<?php
include("db_connection.php");

// Query to get count of each rating (assuming ratings are integers, e.g., 1 to 5)
$rating_query = "
  SELECT rating, COUNT(*) AS count
  FROM feedback
  GROUP BY rating
  ORDER BY rating ASC
";
$rating_result = mysqli_query($conn, $rating_query);

// Prepare an array with all ratings 1-5 initialized to 0 (in case some ratings don't appear)
$ratings_count = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

// Fetch counts from DB and update the array
while ($row = mysqli_fetch_assoc($rating_result)) {
    $ratings_count[(int)$row['rating']] = (int)$row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Feedback Ratings Analytics</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; text-align: center; }
    h1 { margin-bottom: 40px; }
    .chart-container { width: 500px; margin: auto; }
    table { width: 300px; margin: 30px auto; border-collapse: collapse; background: white; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
    th { background-color: #007BFF; color: white; }
  </style>
</head>
<body>
  <h1>Customer Feedback Ratings Analytics</h1>

  <div class="chart-container">
    <canvas id="ratingsChart"></canvas>
  </div>

  <table>
    <thead>
      <tr>
        <th>Rating</th>
        <th>Number of Feedbacks</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($ratings_count as $rating => $count): ?>
      <tr>
        <td><?php echo $rating; ?> Star<?php echo ($rating > 1) ? 's' : ''; ?></td>
        <td><?php echo $count; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <script>
    const ctx = document.getElementById('ratingsChart').getContext('2d');

    const data = {
      labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
      datasets: [{
        label: 'Number of Feedbacks',
        data: [
          <?php echo $ratings_count[1]; ?>,
          <?php echo $ratings_count[2]; ?>,
          <?php echo $ratings_count[3]; ?>,
          <?php echo $ratings_count[4]; ?>,
          <?php echo $ratings_count[5]; ?>
        ],
        backgroundColor: [
          'rgba(255, 99, 132, 0.7)',
          'rgba(255, 159, 64, 0.7)',
          'rgba(255, 205, 86, 0.7)',
          'rgba(75, 192, 192, 0.7)',
          'rgba(54, 162, 235, 0.7)'
        ],
        borderColor: [
          'rgba(255, 99, 132, 1)',
          'rgba(255, 159, 64, 1)',
          'rgba(255, 205, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(54, 162, 235, 1)'
        ],
        borderWidth: 1,
        borderRadius: 5,
        barPercentage: 0.7,
      }]
    };

    const config = {
      type: 'bar',
      data: data,
      options: {
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
              precision: 0
            },
            title: {
              display: true,
              text: 'Number of Feedbacks'
            }
          },
          x: {
            title: {
              display: true,
              text: 'Ratings'
            }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            enabled: true,
            callbacks: {
              label: function(context) {
                return context.parsed.y + ' feedback(s)';
              }
            }
          }
        }
      }
    };

    const ratingsChart = new Chart(ctx, config);
  </script>
</body>
</html>
