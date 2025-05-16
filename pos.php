<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>POS Admin Panel</title>
  <link rel="stylesheet" href="pos.css" />
  <style>
    body { margin: 0; font-family: Arial, sans-serif; background: url('images/a.png') no-repeat center center fixed; background-size: cover; }
    header { background-color: #333; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; position: relative; }
    .notification { position: relative; cursor: pointer; }
    .notification .badge { position: absolute; top: -5px; right: -10px; background: red; color: white; border-radius: 50%; padding: 4px 8px; font-size: 12px; }
    .notification-dropdown {
      display: none; position: absolute; top: 40px; right: 0; background-color: white; color: black;
      border: 1px solid #ccc; box-shadow: 0 2px 10px rgba(0,0,0,0.2); z-index: 999;
      min-width: 350px; max-height: 300px; overflow-y: auto; padding: 10px;
    }
    .notification:hover .notification-dropdown { display: block; }
    .notification-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .notification-table th, .notification-table td {
      border: 1px solid #ddd; padding: 8px; text-align: left;
    }
    .notification-table th { background-color: rgb(137, 127, 127); }
    .admin-container { display: flex; }
    .sidebar {
      width: 220px; background-color: rgba(0, 0, 0, 0.8); color: white; min-height: 100vh; padding: 20px;
    }
    .sidebar h2 { text-align: center; }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar li { margin: 20px 0; }
    .sidebar a { color: white; text-decoration: none; font-weight: bold; }
    .main-content { flex-grow: 1; padding: 30px; background-color: rgba(255, 255, 255, 0.9); }
    form input, form textarea, form button, form select {
      display: block; margin: 10px 0; padding: 10px; width: 100%; max-width: 400px;
    }
    table { width: 100%; border-collapse: collapse; background-color: white; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
    button.edit-btn, .mark-read, .delete-notif {
      padding: 5px 10px; background-color: #4CAF50;
      color: white; border: none; cursor: pointer; border-radius: 4px;
    }
    .mark-read:hover { background-color: #45a049; }
    .delete-notif {
      background-color: #f44336;
    }
    .delete-notif:hover { background-color: #e53935; }
    img { height: 40px; }
    .update-message { color: green; font-weight: bold; }
    .notif-action-btns form { display:inline; margin-right: 5px; }
  </style>
</head>
<body>

<?php
// Handle "Mark as Read"
if (isset($_POST['mark_read'])) {
  $order_id = intval($_POST['order_id']);
  $conn->query("UPDATE orders SET status='Processing' WHERE order_id=$order_id");
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// Delete order from View Orders section
if (isset($_POST['delete_order'])) {
  $delete_order_id = intval($_POST['order_id']);
  $conn->query("DELETE FROM order_items WHERE order_id = $delete_order_id");
  $conn->query("DELETE FROM orders WHERE order_id = $delete_order_id");
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// Delete notification (order)
if (isset($_POST['delete_notif'])) {
  $order_id = intval($_POST['order_id']);
  $conn->query("DELETE FROM orders WHERE order_id=$order_id");
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// Fetch notifications
$notification_query = "SELECT o.order_id, c.customername, o.order_date 
                       FROM orders o 
                       JOIN customers c ON o.customer_id = c.customer_id 
                       WHERE o.status = 'Pending'
                       ORDER BY o.order_date DESC";
$notifications = $conn->query($notification_query);
$notif_count = $notifications->num_rows;

// Add / Update Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
  $product_id = intval($_POST['product_id']);
  $name = $conn->real_escape_string($_POST['product_name']);
  $description = $conn->real_escape_string($_POST['description']);
  $price = floatval($_POST['price']);
  $quantity = intval($_POST['quantity']);
  $image = $conn->real_escape_string($_POST['image_url']);
  $category = $conn->real_escape_string($_POST['category']);
  $unit_option = $conn->real_escape_string($_POST['unit_option']);

  if ($product_id > 0) {
    $sql = "UPDATE products SET 
              name='$name', description='$description', price='$price', 
              quantity='$quantity', image='$image', category='$category', unit_option='$unit_option'
            WHERE product_id=$product_id";
    echo $conn->query($sql) ? "<p class='update-message'>Product updated!</p>" :
                              "<p class='update-message' style='color:red;'>Error: " . $conn->error . "</p>";
  } else {
    $sql = "INSERT INTO products (name, description, price, quantity, image, category, unit_option)
            VALUES ('$name', '$description', $price, $quantity, '$image', '$category', '$unit_option')";
    echo $conn->query($sql) ? "<p class='update-message'>Product added!</p>" :
                              "<p class='update-message' style='color:red;'>Error: " . $conn->error . "</p>";
  }
}

// Update order status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
  $order_id = intval($_POST['order_id']);
  $status = $conn->real_escape_string($_POST['status']);
  $sql = "UPDATE orders SET status='$status' WHERE order_id=$order_id";
  echo $conn->query($sql) ? "<p class='update-message'>Order status updated!</p>" :
                            "<p class='update-message' style='color:red;'>Error: " . $conn->error . "</p>";
}
?>

<header>
  <h1>STORESYNC</h1>
  <div class="notification" title="Order Notifications">
    🔔
    <?php if ($notif_count > 0): ?>
      <span class="badge"><?php echo $notif_count; ?></span>
    <?php endif; ?>
    <div class="notification-dropdown">
      <?php if ($notif_count == 0): ?>
        <p>No new orders</p>
      <?php else: ?>
        <table class="notification-table">
          <thead><tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Actions</th></tr></thead>
          <tbody>
            <?php while ($notif = $notifications->fetch_assoc()): ?>
              <tr>
                <td>#<?php echo $notif['order_id']; ?></td>
                <td><?php echo htmlspecialchars($notif['customername']); ?></td>
                <td><?php echo date('Y-m-d', strtotime($notif['order_date'])); ?></td>
                <td class="notif-action-btns">
                  <form method="POST"><input type="hidden" name="order_id" value="<?php echo $notif['order_id']; ?>">
                    <button class="mark-read" name="mark_read" type="submit">✔️ Mark as Read</button>
                  </form>
                  <form method="POST"><input type="hidden" name="order_id" value="<?php echo $notif['order_id']; ?>">
                    <button class="delete-notif" name="delete_notif" type="submit" onclick="return confirm('Delete this notification?')">🗑️ Delete</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</header>

<div class="admin-container">
  <aside class="sidebar">
    <h2>~POS~</h2>
    <nav>
      <ul>
        <li><a href="#addProduct">➕ Add Products</a></li>
        <li><a href="#viewOrders">📦 View Orders</a></li>
        <li><a href="#inventory">📊 Inventory Status</a></li>
        <li><a href="sales.php">📈 View Sales</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <section id="addProduct">
      <h2>Add / Edit Product</h2>
      <form method="POST">
        <input type="hidden" name="product_id" id="product_id" />
        <input type="text" name="product_name" placeholder="Product Name" required />
        <textarea name="description" placeholder="Product Description" rows="4" required></textarea>
        <input type="number" step="0.01" name="price" placeholder="Price" required />
        <input type="number" name="quantity" placeholder="Quantity" required />
        <input type="url" name="image_url" placeholder="Image URL" />
        <select name="category" required>
          <option value="">Select Category</option>
          <option value="Rice">Rice</option>
          <option value="Soft Drinks">Soft Drinks</option>
          <option value="Canned Goods">Canned Goods</option>
        </select>
        <select name="unit_option" required>
          <option value="">Select Unit</option>
          <option value="Per Kilo">Per Kilo</option>
          <option value="Half Sack">Half Sack</option>
          <option value="1 Sack">1 Sack</option>
          <option value="Per Bottle">Per Bottle</option>
          <option value="Per Case">Per Case</option>
        </select>
        <button type="submit" name="update_product">UPDATE</button>
      </form>
    </section>
<section id="viewOrders" style="margin-top: 50px;">
  <h2>View Orders</h2>
  <?php
  // Fetch orders with customer names
  $orders_sql = "SELECT o.order_id, c.customername, o.order_date, o.status 
                 FROM orders o 
                 JOIN customers c ON o.customer_id = c.customer_id
                 ORDER BY o.order_date DESC";
  $orders_result = $conn->query($orders_sql);

  if ($orders_result->num_rows > 0) {
      echo "<table>";
      echo "<thead><tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Status</th><th>Update Status</th><th>Delete</th></tr></thead><tbody>";

      while ($order = $orders_result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>#".$order['order_id']."</td>";
          echo "<td>".htmlspecialchars($order['customername'])."</td>";
          echo "<td>".date('Y-m-d', strtotime($order['order_date']))."</td>";
          echo "<td>".$order['status']."</td>";

          echo "<td>
                  <form method='POST'>
                    <input type='hidden' name='order_id' value='".$order['order_id']."' />
                    <select name='status' required>
                      <option value='Pending' ".($order['status']=='Pending'?'selected':'').">Pending</option>
                      <option value='Processing' ".($order['status']=='Processing'?'selected':'').">Processing</option>
                      <option value='Completed' ".($order['status']=='Completed'?'selected':'').">Completed</option>
                    </select>
                    <button type='submit' name='update_status'>Update</button>
                  </form>
                </td>";

          echo "<td>
                  <form method='POST' onsubmit=\"return confirm('Delete this order?');\">
                    <input type='hidden' name='order_id' value='".$order['order_id']."' />
                    <button type='submit' name='delete_order'>Delete</button>
                  </form>
                </td>";
          echo "</tr>";
      }
      echo "</tbody></table>";
  } else {
      echo "<p>No orders found.</p>";
  }
  ?>
</section>

<section id="inventory" style="margin-top: 50px;">
  <h2>Inventory Status</h2>
  <?php
  $products_sql = "SELECT * FROM products ORDER BY category, name";
  $products_result = $conn->query($products_sql);

  if ($products_result->num_rows > 0) {
      echo "<table>";
      echo "<thead>";
      echo "<tr>";
      echo "<th>Product</th>";
      echo "<th>Category</th>";
      echo "<th>Price</th>";
      echo "<th>Description</th>";
      echo "<th>In Stock</th>";
      echo "<th>Image</th>";
      echo "<th>Action</th>";
      echo "</tr>";
      echo "</thead><tbody>";

      while ($p = $products_result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($p['name']) . "</td>";
          echo "<td>" . htmlspecialchars($p['category']) . "</td>";
          echo "<td>₱" . number_format($p['price'], 2) . "</td>";
          echo "<td>" . htmlspecialchars($p['description']) . "</td>";
          echo "<td>" . intval($p['quantity']) . "</td>";
          echo "<td><img src='" . htmlspecialchars($p['image']) . "' alt='Product Image' style='max-width:100px; height:auto;' /></td>";
          echo "<td>
                  <button class='edit-btn' onclick=\"editProduct('{$p['product_id']}', '".addslashes($p['name'])."', '{$p['price']}', '{$p['quantity']}', '".addslashes($p['description'])."', '".addslashes($p['image'])."', '".addslashes($p['category'])."')\">Edit</button>
                  <form action='delete_product.php' method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this product?\");'>
                    <input type='hidden' name='product_id' value='{$p['product_id']}'>
                    <button type='submit' class='delete-btn'>Delete</button>
                  </form>
                </td>";
          echo "</tr>";
      }
      echo "</tbody></table>";
  } else {
      echo "<p>No products found.</p>";
  }
  ?>
</section>

<script>
function editProduct(id, name, price, quantity, description, image, category) {
  document.getElementById('product_id').value = id;
  document.querySelector('input[name="product_name"]').value = name;
  document.querySelector('input[name="price"]').value = price;
  document.querySelector('input[name="quantity"]').value = quantity;
  document.querySelector('textarea[name="description"]').value = description;
  document.querySelector('input[name="image_url"]').value = image;
  document.querySelector('select[name="category"]').value = category;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
</body> 
</html>