<?php 
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: user_login.php');
    exit();
}
$customer_id = $_SESSION['customer_id'];
$customername = $_SESSION['customername'];

// Include the database connection
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
      background: #f4f4f4;
      padding: 10px;
    }
    .main-content {
      margin-left: 220px;
      padding: 20px;
    }
    button {
      cursor: pointer;
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
    }
    #checkoutForm select, #checkoutForm input {
      width: 100%;
      padding: 5px;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Menu</h2>
    <button onclick="window.location.href='profile.php'">👤 Profile</button>
    <button onclick="window.location.href='customer_purchase.php'">📜 Order History</button>

    <button onclick="window.location.href='logout.php'">🚪 Logout</button>
  </div>

  <!-- Main content -->
  <div class="main-content">
    <!-- Header -->
    <div class="header" style="display:flex; justify-content:space-between; align-items:center;">
      <h1>STORESYNC - Welcome, <?php echo htmlspecialchars($customername); ?>!</h1>
      <div class="header-icons">
        <div class="notifications" onclick="alert('You have no new notifications!')">🔔</div>
        <div class="cart-icon" onclick="toggleCart()">🛒</div>
      </div>
    </div>

    <!-- Cart summary box -->
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
          <tbody id="cartItems">
            <tr><td colspan="5" class="empty">Your cart is empty.</td></tr>
          </tbody>
        </table>
      </div>
      <div style="margin-top:10px;">
        <strong>Total: ₱<span id="cartTotal">0.00</span></strong>
      </div>
      <button style="margin-top:10px;" onclick="checkoutCart()">Checkout</button>
    </div>

    <!-- Checkout form modal -->
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

    <!-- Page Content -->
    <div class="content">
      <h2>Product List</h2>
      <div class="product-container">
        <?php
        $query = "SELECT * FROM products";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) {
                echo "<div class='product-card'>
                        <img src='{$product['image']}' alt='{$product['name']}' />
                        <h3>{$product['name']}</h3>
                        <p>{$product['description']}</p>
                        <div class='price'>₱{$product['price']}</div>
                        <button onclick='addToCart({$product['product_id']}, \"{$product['name']}\", {$product['price']})'>Add to Cart</button>
                      </div>";
            }
        } else {
            echo "<p>No products available.</p>";
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
      alert('Added ' + name + ' to cart!');
    }

    function changeQuantity(productId, newQuantity) {
      newQuantity = parseInt(newQuantity);
      if (isNaN(newQuantity) || newQuantity < 1) {
        deleteItem(productId);
        return;
      }
      const item = cart.find(i => i.productId === productId);
      if (item) {
        item.quantity = newQuantity;
        saveCart();
      }
    }

    function deleteItem(productId) {
      cart = cart.filter(i => i.productId !== productId);
      saveCart();
    }

    function updateCartDisplay() {
      const cartItemsEl = document.getElementById('cartItems');
      const cartTotalEl = document.getElementById('cartTotal');
      cartItemsEl.innerHTML = '';
      let total = 0;

      if (cart.length === 0) {
        cartItemsEl.innerHTML = '<tr><td colspan="5" class="empty">Your cart is empty.</td></tr>';
      } else {
        cart.forEach(item => {
          const subtotal = item.price * item.quantity;
          total += subtotal;
          cartItemsEl.innerHTML += `
            <tr>
              <td>${item.name}</td>
              <td>
                <input type="number" min="1" value="${item.quantity}" 
                  onchange="changeQuantity(${item.productId}, this.value)">
              </td>
              <td>₱${item.price.toFixed(2)}</td>
              <td>₱${subtotal.toFixed(2)}</td>
              <td><button onclick="deleteItem(${item.productId})">🗑️</button></td>
            </tr>
          `;
        });
      }

      cartTotalEl.textContent = total.toFixed(2);
    }

    function toggleCart() {
      const cartSummary = document.getElementById('cartSummary');
      if (cartSummary.style.display === 'none' || cartSummary.style.display === '') {
        updateCartDisplay();
        cartSummary.style.display = 'block';
      } else {
        cartSummary.style.display = 'none';
      }
    }

    function checkoutCart() {
      if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
      }
      document.getElementById('checkoutForm').style.display = 'block';
    }

    function submitCheckout() {
      const paymentMode = document.getElementById('paymentMode').value;
      const deliveryMode = document.getElementById('deliveryMode').value;
      const preferredTime = document.getElementById('preferredTime').value;

      if (!preferredTime) {
        alert('Please select a preferred date and time.');
        return;
      }

      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'checkout.php', true);
      xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');

      xhr.onload = function() {
        if (xhr.status === 200) {
          alert('Checkout successful! Thank you for your order.');
          cart = [];
          saveCart();
          document.getElementById('checkoutForm').style.display = 'none';
          toggleCart();
        } else {
          alert('Checkout failed. Please try again.');
        }
      };

      xhr.send(JSON.stringify({
        customer_id: <?php echo json_encode($customer_id); ?>,
        cart: cart,
        payment_mode: paymentMode,
        delivery_mode: deliveryMode,
        preferred_time: preferredTime
      }));
    }

    window.onload = () => {
      updateCartDisplay();
    };
  </script>

</body>
</html>
