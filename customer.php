<?php
include("db_connection.php");

// Fetch latest 5 feedback entries
$feedback_query = "SELECT f.feedback_id, f.feedback AS comment, f.rating, c.customername, f.created_at, f.status, f.customer_reply 
                   FROM feedback f 
                   JOIN customers c ON f.customer_id = c.customer_id
                   ORDER BY f.created_at DESC LIMIT 5";
$feedback_result = mysqli_query($conn, $feedback_query);
$feedbacks = mysqli_fetch_all($feedback_result, MYSQLI_ASSOC);

// Count unread notifications (only those with status 'unread' and notification = 0)
$notif_query = "SELECT COUNT(*) as unread_count FROM feedback 
                WHERE status = 'unread' AND notification = 0";
$notif_result = mysqli_query($conn, $notif_query);
$notif_data = mysqli_fetch_assoc($notif_result);
$unread_count = $notif_data['unread_count'];

// Fetch unread feedbacks/replies for notification modal
$notif_details_query = "SELECT f.feedback_id, c.customername, f.feedback, f.customer_reply, f.created_at, f.status 
                        FROM feedback f 
                        JOIN customers c ON f.customer_id = c.customer_id
                        WHERE f.notification = 0
                        ORDER BY f.created_at DESC";
$notif_details_result = mysqli_query($conn, $notif_details_query);
$notif_items = mysqli_fetch_all($notif_details_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard - Customer Feedback</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    /* Add all your styles here (same as previous version for brevity)... */
    body {
      font-family: Arial, sans-serif;
      margin: 0; padding: 0;
      background: url('images/a.png') no-repeat center center fixed;
      background-size: cover;
      color: #333;
    }
    .sidebar {
      width: 250px;
      background-color: #333;
      color: white;
      position: fixed;
      top: 0; bottom: 0; left: 0;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .sidebar h2 {
      color: #f0c040;
      text-align: center;
    }
    .sidebar nav a {
      display: block;
      color: white;
      text-decoration: none;
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      font-size: 18px;
      transition: background 0.3s ease;
    }
    .sidebar nav a:hover {
      background-color: #444;
    }
    .logout-button {
      width: 100%;
      padding: 10px;
      background: none;
      border: none;
      font-size: 16px;
      cursor: pointer;
      margin-top: 20px;
      color: black;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .logout-button i { color: red; }

    .header {
      margin-left: 250px;
      padding: 10px 20px;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      position: relative;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .notification-bell {
      position: relative;
      font-size: 24px;
      cursor: pointer;
      color: #333;
    }
    .notification-count {
      position: absolute;
      top: -5px; right: -10px;
      background-color: red;
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 12px;
    }

    .overlay {
      margin-left: 250px;
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.85);
      min-height: 100vh;
    }
    .dashboard {
      max-width: 1000px;
      margin: auto;
    }
    .card {
      background: white;
      padding: 50px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 25px;
    }
    .card h2 {
      margin-top: 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      border-bottom: 1px solid #ccc;
      padding: 12px;
      text-align: left;
    }
    th {
      background-color: #eee;
    }
    .action-buttons button {
      padding: 6px 12px;
      margin-right: 5px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
    }
    .reply { background-color: #4CAF50; color: white; }
    .delete { background-color: #e74c3c; color: white; }

    .modal {
      display: none;
      position: fixed;
      top: 60px;
      right: 20px;
      width: 320px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
      z-index: 999;
      padding: 15px;
      max-height: 400px;
      overflow-y: auto;
    }
    .modal h3 {
      margin-top: 0;
      font-size: 18px;
    }
    .notif-item {
      border-bottom: 1px solid #ccc;
      padding: 10px 0;
    }
    .notif-item:last-child {
      border-bottom: none;
    }
    .notif-item button {
      margin-top: 6px;
      margin-right: 5px;
      padding: 5px 10px;
      font-size: 12px;
      border-radius: 4px;
      border: none;
      cursor: pointer;
    }
    .modal table th, .modal table td {
  padding: 8px;
  font-size: 14px;
  vertical-align: top;
}

.modal table tr.unread {
  font-weight: bold;
  background-color: #f9f9f9;
}

.modal table tr.read {
  color: #555;
}

    .mark-read-btn { background-color: #4CAF50; color: white; }
    .delete-notif-btn { background-color: #e74c3c; color: white; }
    .notif-item.unread { font-weight: bold; }
    .notif-item.read { font-weight: normal; color: #555; }
  </style>
</head>
<body>

<div class="sidebar">
  <div>
    <h2>DASHBOARD</h2>
    <nav>
      <a href="#">Dashboard</a>
      <a href="#">Customer Feedback</a>
    <a href="customer_analytics.php" style="display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">Analytics</a>

      <a href="admin_messages.php" style="display: inline-block; padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Messages</a>

    </nav>
  </div>
  <button class="logout-button" onclick="logout()">
    <i class="fas fa-sign-out-alt"></i> Logout
  </button>
</div>

<div class="header">
  <div class="notification-bell" onclick="toggleNotifications()">
    <i class="fas fa-bell"></i>
    <?php if ($unread_count > 0): ?>
      <span class="notification-count" id="notifCount"><?php echo $unread_count; ?></span>
    <?php endif; ?>
  </div>

  <div class="modal" id="notifModal">
  <h3>Notifications</h3>
  <?php if (count($notif_items) > 0): ?>
    <table style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th style="text-align:left; border-bottom: 1px solid #ccc;">Customer</th>
          <th style="text-align:left; border-bottom: 1px solid #ccc;">Message</th>
          <th style="text-align:left; border-bottom: 1px solid #ccc;">Date</th>
          <th style="text-align:left; border-bottom: 1px solid #ccc;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($notif_items as $item): 
          $is_unread = ($item['status'] === 'unread');
          $item_class = $is_unread ? 'unread' : 'read';
        ?>
        <tr class="notif-item <?php echo $item_class; ?>" data-feedback-id="<?php echo $item['feedback_id']; ?>">
          <td><?php echo htmlspecialchars($item['customername']); ?></td>
          <td>
            <?php if (!empty($item['customer_reply'])): ?>
              <em>replied:</em> "<?php echo htmlspecialchars($item['customer_reply']); ?>"
            <?php else: ?>
              <em>left feedback:</em> "<?php echo htmlspecialchars($item['feedback']); ?>"
            <?php endif; ?>
          </td>
          <td><small><?php echo $item['created_at']; ?></small></td>
          <td>
            <?php if ($is_unread): ?>
              <button class="mark-read-btn" data-id="<?php echo $item['feedback_id']; ?>">Mark as Read</button>
            <?php endif; ?>
            <button class="delete-notif-btn" data-id="<?php echo $item['feedback_id']; ?>">Delete</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No new notifications.</p>
  <?php endif; ?>
</div>

</div>

<div class="overlay">
  <div class="dashboard">
    <div class="card">
      <h2>Customer Feedback</h2>
      <table>
        <thead>
          <tr>
            <th>Customer</th>
            <th>Comments</th>
            <th>Rating</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($feedbacks as $feedback): ?>
            <tr>
              <td><?php echo htmlspecialchars($feedback['customername']); ?></td>
              <td><?php echo htmlspecialchars($feedback['comment']); ?></td>
              <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
              <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
              <td class="action-buttons">
                <button class="reply" onclick="location.href='reply_feedback.php?feedback_id=<?php echo $feedback['feedback_id']; ?>'">Reply</button>
                <button class="delete" onclick="if(confirm('Delete this feedback?')) location.href='dashboard.php?delete_feedback_id=<?php echo $feedback['feedback_id']; ?>';">Delete</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function toggleNotifications() {
  const modal = document.getElementById('notifModal');
  modal.style.display = modal.style.display === 'block' ? 'none' : 'block';
}

function logout() {
  alert("Logging out...");
}

// Mark as Read button handler
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.mark-read-btn').forEach(button => {
    button.addEventListener('click', function () {
      const feedbackId = this.dataset.id;
      const notifItem = this.closest('.notif-item');
      fetch('mark-as-read.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'feedback_id=' + encodeURIComponent(feedbackId)
      })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === 'success') {
          notifItem.classList.remove('unread');
          notifItem.classList.add('read');
          this.remove(); // remove mark as read button
          updateNotifCount(-1);
        } else {
          alert('Failed to mark as read.');
        }
      });
    });
  });

  // Delete notification handler
  document.querySelectorAll('.delete-notif-btn').forEach(button => {
    button.addEventListener('click', function () {
      if (!confirm('Delete this notification?')) return;
      const feedbackId = this.dataset.id;
      const notifItem = this.closest('.notif-item');
      fetch('delete-feedback.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'feedback_id=' + encodeURIComponent(feedbackId)
      })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === 'success') {
          if (notifItem.classList.contains('unread')) {
            updateNotifCount(-1);
          }
          notifItem.remove();
        } else {
          alert('Failed to delete notification.');
        }
      });
    });
  });
});

function updateNotifCount(change) {
  const countSpan = document.getElementById('notifCount');
  if (countSpan) {
    let count = parseInt(countSpan.innerText);
    count += change;
    if (count <= 0) {
      countSpan.remove();
    } else {
      countSpan.innerText = count;
    }
  }
}
</script>

</body>
</html>
