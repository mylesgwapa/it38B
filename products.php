<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: user_login.php');
    exit();
}
$customer_id = $_SESSION['customer_id'];
$customername = $_SESSION['customername'];

include 'db_connection.php';

// Fetch admin replies as notifications
$notif_query = "SELECT feedback_id, reply, reply_read FROM feedback WHERE customer_id = $customer_id AND reply IS NOT NULL";
$notif_result = mysqli_query($conn, $notif_query);
$notifications = mysqli_fetch_all($notif_result, MYSQLI_ASSOC);

$unreadCount = 0;
foreach ($notifications as $notif) {
    if ($notif['reply_read'] == 0) {
        $unreadCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="products.css">
  <title>All Products</title>
  <style>
      body {
      font-family: Arial, sans-serif;
    }
    .header-icons {
      display: flex;
      gap: 15px;
      cursor: pointer;
      position: relative;
    }
    .notifications {
      position: relative;
      font-size: 24px;
      user-select: none;
    }
    .notifications span {
      position: absolute;
      top: -5px;
      right: -8px;
      color: red;
      font-size: 18px;
      font-weight: bold;
    }
    #notifBox {
      display:none; 
      position:absolute; 
      right:80px; 
      top:70px; 
      width:360px; 
      background:#fff; 
      border:1px solid #ccc; 
      box-shadow:0 4px 10px rgba(0,0,0,0.1); 
      z-index:1000; 
      padding:10px;
      border-radius: 6px;
      max-height: 400px;
      overflow-y: auto;
    }
    #notifBox ul {
      list-style: none;
      padding-left: 0;
      margin: 0;
    }
    #notifBox li {
      margin-bottom: 15px;
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
      cursor: default;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    #notifBox li.unread > .notif-text {
      font-weight: bold;
    }
    #notifBox li.read > .notif-text {
      font-weight: normal;
    }
    #notifBox li:hover {
      background-color: #f0f0f0;
    }
    .notif-buttons button {
      margin-left: 5px;
      background: #007bff;
      border: none;
      color: white;
      padding: 4px 8px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }
    .notif-buttons button.delete-btn {
      background: #dc3545;
    }
    .notif-buttons button:hover {
      opacity: 0.9;
    }
    /* rest unchanged */
    .product-container {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }
    .product-card {
      border: 1px solid #ddd;
      padding: 10px;
      width: 200px;
      text-align: center;
    }
    .product-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
    }
    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      width: 200px;
      height: 100%;
      background:rgb(46, 44, 44);
      padding: 10px;
      color: white;
      box-sizing: border-box;
    }
    .sidebar button {
      width: 100%;
      margin-bottom: 10px;
      background: #333;
      color: white;
      border: none;
      padding: 10px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 4px;
    }
    .sidebar button:hover {
      background: #555;
    }
    .main-content {
      margin-left: 220px;
      padding: 20px;
    }
    button {
      cursor: pointer;
    }
    .cart-summary {
      position: absolute;
      right: 20px;
      top: 70px;
      width: 450px;
      background: #fff;
      border: 1px solid #ddd;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 10px;
      display: none;
      z-index: 1000;
    }
    .cart-summary table {
      width: 100%;
      border-collapse: collapse;
    }
    .cart-summary th, .cart-summary td {
      border: 1px solid #eee;
      padding: 5px;
      text-align: center;
    }
    .cart-summary input[type="number"] {
      width: 60px;
    }
    .cart-summary .empty {
      text-align: center;
      color: #888;
    }
    #checkoutForm {
      display:none; 
      position:fixed; 
      top:50%; left:50%; 
      transform:translate(-50%,-50%); 
      background:#fff; 
      border:1px solid #ddd; 
      padding:20px; 
      z-index:2000;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      width: 300px;
      border-radius: 8px;
    }
    #checkoutForm select, #checkoutForm input {
      width: 100%;
      padding: 5px;
      margin-top: 5px;
      box-sizing: border-box;
    } /* [Style omitted for brevity: identical to your working styles above] */
    /* Category Filter Styling */
/* Filter Form Styling */
form[method="GET"] {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

form[method="GET"] label {
  font-weight: bold;
  font-size: 16px;
}

form[method="GET"] select {
  padding: 5px 10px;
  font-size: 16px;
  border-radius: 5px;
  border: 1px solid #ccc;
  outline: none;
}

form[method="GET"] button {
  padding: 6px 15px;
  font-size: 16px;
  background-color: #2ecc71;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s;
}

form[method="GET"] button:hover {
  background-color: #27ae60;
}


  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Menu</h2>
    <button onclick="window.location.href='profile.php'">👤 Profile</button>
    <button onclick="window.location.href='customer_purchase.php'">📜 Order History</button>
    <button onclick="window.location.href='messages.php'">💬 Messages</button>
    <button onclick="window.location.href='logout.php'">🚪 Logout</button>
  </div>

  <div class="main-content">
    <div class="header" style="display:flex; justify-content:space-between; align-items:center;">
      <h1>STORESYNC - Welcome, <?php echo htmlspecialchars($customername); ?>!</h1>
      <div class="header-icons">
        <div class="notifications" onclick="toggleNotifications()">🔔
          <?php if ($unreadCount > 0) echo "<span id='notifCount'>$unreadCount</span>"; ?>
        </div>
        <div class="cart-icon" onclick="toggleCart()" style="font-size:24px; cursor:pointer;">🛒</div>
      </div>
    </div>

    <div id="notifBox">
      <h4>Notifications</h4>
      <?php if (count($notifications) > 0): ?>
      <ul id="notifList">
        <?php foreach ($notifications as $notif): ?>
          <li id="notif-<?php echo $notif['feedback_id']; ?>" class="<?php echo ($notif['reply_read'] == 0) ? 'unread' : 'read'; ?>">
            <div class="notif-text">📝 Admin replied: "<?php echo htmlspecialchars($notif['reply']); ?>"</div>
            <div class="notif-buttons">
              <button onclick="openNotification(<?php echo $notif['feedback_id']; ?>)">Open</button>
              <button class="delete-btn" onclick="deleteNotification(event, <?php echo $notif['feedback_id']; ?>)">Delete</button>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php else: ?>
        <p>No notifications.</p>
      <?php endif; ?>
    </div>

    <div id="cartSummary" class="cart-summary">
      <h4>Your Cart</h4>
      <div style="overflow-x:auto;">
        <table>
          <thead>
            <tr>
              <th>Product</th>
              <th>Qty</th>
              <th>Price</th>
              <th>Subtotal</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="cartItems"></tbody>
        </table>
      </div>
      <div style="margin-top:10px;">
        <strong>Total: ₱<span id="cartTotal">0.00</span></strong>
      </div>
      <button style="margin-top:10px;" onclick="checkoutCart()">Checkout</button>
    </div>

    <div id="checkoutForm">
      <h3>Checkout Details</h3>
      <label>Mode of Payment:</label>
      <select id="paymentMode">
        <option value="GCash">GCash</option>
        <option value="PayMaya">PayMaya</option>
        <option value="Bank">Bank</option>
        <option value="Cash on Hand">Cash on Hand</option>
      </select>
      <br><br>

      <label>Mode of Delivery:</label>
      <select id="deliveryMode">
        <option value="Delivery">Delivery</option>
        <option value="Pick Up">Pick Up</option>
      </select>
      <br><br>

      <label>Preferred Date & Time:</label>
      <input type="datetime-local" id="preferredTime">
      <br><br>

      <button onclick="submitCheckout()">Submit Order</button>
      <button onclick="document.getElementById('checkoutForm').style.display='none'">Cancel</button>
    </div>

    <div class="content">
<h2>Product List</h2>

<!-- Category Filter UI -->
<form method="GET" style="margin-bottom: 20px;">
  <label for="category">Filter by Category:</label>
  <select name="category" id="category">
    <option value="">All</option>
    <?php
      $cat_query = "SELECT DISTINCT category FROM products";
      $cat_result = mysqli_query($conn, $cat_query);
      while ($cat_row = mysqli_fetch_assoc($cat_result)) {
        $selected = ($_GET['category'] ?? '') === $cat_row['category'] ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($cat_row['category']) . "' $selected>" . htmlspecialchars($cat_row['category']) . "</option>";
      }
    ?>
  </select>
  <button type="submit">Apply</button>
</form>

<!-- Product Cards -->
<div class="product-container">
  <?php
  $selectedCategory = $_GET['category'] ?? '';
  $query = "SELECT * FROM products";
  if (!empty($selectedCategory)) {
      $safeCategory = mysqli_real_escape_string($conn, $selectedCategory);
      $query .= " WHERE category = '$safeCategory'";
  }
  $result = $conn->query($query);

  if ($result->num_rows > 0) {
      while ($product = $result->fetch_assoc()) {
          $pid = htmlspecialchars($product['product_id']);
          $pname = htmlspecialchars($product['name']);
          $pdesc = htmlspecialchars($product['description']);
          $pimage = htmlspecialchars($product['image']);
          $pprice = number_format($product['price'], 2);
          $pquantity = (int)$product['quantity'];

          $outOfStock = $pquantity === 0;
          $buttonHTML = $outOfStock
            ? '<button disabled style="background-color:gray; cursor:not-allowed;">Out of Stock</button>'
            : "<button onclick='addToCart({$pid}, \"{$pname}\", {$product['price']})'>Add to Cart</button>";

          echo "<div class='product-card'>
                  <img src='{$pimage}' alt='{$pname}' />
                  <h3>{$pname}</h3>
                  <p>{$pdesc}</p>
                  <div class='price'>₱{$pprice}</div>
                  <div class='quantity'>Quantity: {$pquantity}</div>
                  {$buttonHTML}
                </div>";
      }
  } else {
      echo "<p>No products found in this category.</p>";
  }
  ?>
</div>


    </div>
  </div>

  <script>
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    function saveCart() {
      localStorage.setItem('cart', JSON.stringify(cart));
      updateCartDisplay();
    }

    function addToCart(productId, name, price) {
      const existing = cart.find(item => item.productId === productId);
      if (existing) {
        existing.quantity += 1;
      } else {
        cart.push({ productId, name, price, quantity: 1 });
      }
      saveCart();
    }

    function updateCartDisplay() {
      const cartItemsContainer = document.getElementById("cartItems");
      const cartTotalSpan = document.getElementById("cartTotal");
      cartItemsContainer.innerHTML = "";

      if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<tr><td colspan="5" class="empty">Your cart is empty.</td></tr>';
        cartTotalSpan.textContent = "0.00";
        return;
      }

      let total = 0;
      cart.forEach((item, index) => {
        const subtotal = item.price * item.quantity;
        total += subtotal;
        const row = `<tr>
          <td>${item.name}</td>
          <td><input type="number" value="${item.quantity}" min="1" onchange="changeQuantity(${index}, this.value)" /></td>
          <td>₱${item.price.toFixed(2)}</td>
          <td>₱${subtotal.toFixed(2)}</td>
          <td><button onclick="removeFromCart(${index})">Remove</button></td>
        </tr>`;
        cartItemsContainer.innerHTML += row;
      });
      cartTotalSpan.textContent = total.toFixed(2);
    }

    function changeQuantity(index, newQty) {
      cart[index].quantity = parseInt(newQty);
      saveCart();
    }

    function removeFromCart(index) {
      cart.splice(index, 1);
      saveCart();
    }

    function toggleCart() {
      const cartBox = document.getElementById("cartSummary");
      cartBox.style.display = cartBox.style.display === 'none' ? 'block' : 'none';
      updateCartDisplay();
    }

    function toggleNotifications() {
      const notifBox = document.getElementById("notifBox");
      notifBox.style.display = notifBox.style.display === 'none' ? 'block' : 'none';
    }

    function checkoutCart() {
      document.getElementById('checkoutForm').style.display = 'block';
    }

    function submitCheckout() {
      const paymentMode = document.getElementById("paymentMode").value;
      const deliveryMode = document.getElementById("deliveryMode").value;
      const preferredTime = document.getElementById("preferredTime").value;
      const payload = {
        customer_id: <?php echo $customer_id; ?>,
        cart: cart.map(item => ({
          product_id: item.productId,
          quantity: item.quantity,
          price: item.price
        })),
        payment_mode: paymentMode,
        delivery_mode: deliveryMode,
        preferred_time: preferredTime
      };

      fetch('checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert("Order placed successfully!");
          cart = [];
          saveCart();
          document.getElementById('checkoutForm').style.display = 'none';
        }
      });
    }

    window.onload = updateCartDisplay;
  </script>
</body>
</html>
