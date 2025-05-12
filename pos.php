<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>POS Admin Panel</title>
  <link rel="stylesheet" href="pos.css" />
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('images/a.png') no-repeat center center fixed;
      background-size: cover;
    }
    .admin-container { display: flex; }
    .sidebar {
      width: 220px; background-color: rgba(0, 0, 0, 0.8);
      color: white; min-height: 100vh; padding: 20px;
    }
    .sidebar h2 { text-align: center; }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar li { margin: 20px 0; }
    .sidebar a { color: white; text-decoration: none; font-weight: bold; }
    .main-content { flex-grow: 1; padding: 30px; background-color: rgba(255, 255, 255, 0.9); }
    form input, form textarea, form button, form select {
      display: block; margin: 10px 0; padding: 10px;
      width: 100%; max-width: 400px;
    }
    textarea { resize: vertical; }
    table { width: 100%; border-collapse: collapse; background-color: white; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
    button.edit-btn {
      padding: 5px 10px; background-color: #4CAF50;
      color: white; border: none; cursor: pointer; border-radius: 4px;
    }
    button.edit-btn:hover { background-color: #45a049; }
    button.delete-btn {
      padding: 5px 10px; background-color: #f44336;
      color: white; border: none; cursor: pointer; border-radius: 4px;
    }
    button.delete-btn:hover { background-color: #e53935; }
    hr { margin: 40px 0; }
    img { height: 40px; }
    .update-message { color: green; font-weight: bold; }
  </style>
</head>
<body>

<div class="admin-container">
  <aside class="sidebar">
    <h2>POS INTEGRATION</h2>
    <nav>
      <ul>
        <li><a href="#addProduct">➕ Add Products</a></li>
        <li><a href="#viewOrders">📦 View Orders</a></li>
        <li><a href="#inventory">📊 Inventory Status</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
      $product_id = $_POST['product_id'];
      $name = $_POST['product_name'];
      $description = $_POST['description'];
      $price = $_POST['price'];
      $quantity = $_POST['quantity'];
      $image = $_POST['image_url'];

      $update_sql = "UPDATE products SET name='$name', description='$description', price='$price', quantity='$quantity', image='$image' WHERE product_id='$product_id'";
      if ($conn->query($update_sql) === TRUE) {
        echo "<p class='update-message'>Product updated successfully!</p>";
      } else {
        echo "<p class='update-message' style='color:red;'>Error updating product: " . $conn->error . "</p>";
      }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
      $order_id = $_POST['order_id'];
      $status = $_POST['status'];

      $update_order_sql = "UPDATE orders SET status='$status' WHERE order_id='$order_id'";
      if ($conn->query($update_order_sql) === TRUE) {
        echo "<p class='update-message'>Order status updated successfully!</p>";
      } else {
        echo "<p class='update-message' style='color:red;'>Error updating order status: " . $conn->error . "</p>";
      }
    }
    ?>

    <section id="addProduct">
      <h2>Add / Edit Product</h2>
      <form action="" method="POST">
        <input type="hidden" name="product_id" id="product_id" />
        <input type="text" name="product_name" placeholder="Product Name" required />
        <textarea name="description" placeholder="Product Description" rows="4" required></textarea>
        <input type="number" name="price" placeholder="Price" required />
        <input type="number" name="quantity" placeholder="Quantity" required />
        <input type="url" name="image_url" placeholder="Image URL" />
        <button type="submit" name="update_product">UPDATE</button>
      </form>
    </section>

    <hr>

    <section id="inventory">
      <h2>Inventory Status</h2>
      <table>
        <thead>
          <tr>
            <th>Product</th><th>Price</th><th>Description</th><th>In Stock</th><th>Image</th><th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $products = $conn->query("SELECT * FROM products");
       while ($p = $products->fetch_assoc()) {
  echo '<tr>
    <td>' . $p['name'] . '</td>
    <td>₱' . $p['price'] . '</td>
    <td>' . $p['description'] . '</td>
    <td>' . $p['quantity'] . '</td>
    <td><img src="' . $p['image'] . '" /></td>
    <td>
      <button class="edit-btn" onclick="editProduct('
        . $p['product_id'] . ', '
        . '\'' . addslashes($p['name']) . '\', '
        . '\'' . $p['price'] . '\', '
        . '\'' . $p['quantity'] . '\', '
        . '\'' . addslashes($p['description']) . '\', '
        . '\'' . addslashes($p['image']) . '\')">Edit</button>
      <form action="delete_product.php" method="POST" style="display:inline;">
        <input type="hidden" name="product_id" value="' . $p['product_id'] . '">
        <button type="submit" class="delete-btn" onclick="return confirm(\'Are you sure you want to delete this product?\')">Delete</button>
      </form>
    </td>
  </tr>';
}

          ?>
        </tbody>
      </table>
    </section>

    <hr>

    <section id="viewOrders">
  <h2>View Orders</h2>
  <table>
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Customer Name</th>
        <th>Items</th>
        <th>Total Amount</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $orders = $conn->query("SELECT o.*, c.customername  
        FROM orders o 
        JOIN customers c ON o.customer_id = c.customer_id 
        ORDER BY o.order_date DESC");

      while ($order = $orders->fetch_assoc()) {
        $order_id = $order['order_id'];
        $items = $conn->query("SELECT oi.*, p.name 
                               FROM order_items oi 
                               JOIN products p ON oi.product_id = p.product_id 
                               WHERE oi.order_id = $order_id");

        $itemList = "";
        while ($item = $items->fetch_assoc()) {
          $itemList .= $item['name'] . " (x" . $item['quantity'] . ")<br>";
        }

        echo "<tr>
          <td>{$order['order_id']}</td>
          <td>{$order['customername']}</td>
          <td>$itemList</td>
          <td>₱" . number_format($order['total_amount'], 2) . "</td>
          <td>
            <form method='POST'>
              <input type='hidden' name='order_id' value='{$order['order_id']}'>
              <select name='status'>
                <option " . ($order['status'] == 'Pending' ? 'selected' : '') . ">Pending</option>
                <option " . ($order['status'] == 'Processing' ? 'selected' : '') . ">Processing</option>
                <option " . ($order['status'] == 'Out for Delivery' ? 'selected' : '') . ">Out for Delivery</option>
                <option " . ($order['status'] == 'Completed' ? 'selected' : '') . ">Completed</option>
                <option " . ($order['status'] == 'Cancelled' ? 'selected' : '') . ">Cancelled</option>
              </select>
              <button type='submit' name='update_status' class='edit-btn'>Update</button>
            </form>
          </td>
          <td>
            <form action='delete_order.php' method='POST' style='display:inline;'>
              <input type='hidden' name='order_id' value='{$order['order_id']}' />
              <button type='submit' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this order?\")'>Delete</button>
            </form>
          </td>
        </tr>";
      }
      ?>
    </tbody>
  </table>
</section>

  </main>
</div>

<script>
function editProduct(id, name, price, quantity, description, image) {
  document.getElementById('product_id').value = id;
  document.querySelector('[name=product_name]').value = name;
  document.querySelector('[name=price]').value = price;
  document.querySelector('[name=quantity]').value = quantity;
  document.querySelector('[name=description]').value = description;
  document.querySelector('[name=image_url]').value = image;
}
</script>

</body>
</html>
