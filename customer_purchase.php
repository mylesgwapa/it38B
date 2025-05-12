<?php
include 'db_connection.php';

// Use order_id from localStorage via JavaScript
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Order</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .order-summary, .order-item { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; border-radius: 6px; }
        .order-item { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h2>📦 Your Order Details</h2>
    <div id="order-details">Loading...</div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const orderId = localStorage.getItem("order_id");
        if (!orderId) {
            document.getElementById("order-details").innerHTML = "<p>No recent order found.</p>";
            return;
        }

        fetch(`get_order_details.php?order_id=${orderId}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    document.getElementById("order-details").innerHTML = "<p>Order not found.</p>";
                    return;
                }

                const order = data.order;
                const items = data.items;
                let html = `
                    <div class="order-summary">
                        <p><strong>Order ID:</strong> ${order.order_id}</p>
                        <p><strong>Status:</strong> ${order.status}</p>
                        <p><strong>Order Date:</strong> ${order.order_date}</p>
                        <p><strong>Total:</strong> ₱${order.total_amount.toFixed(2)}</p>
                    </div>
                    <h3>🛒 Items:</h3>
                `;

                items.forEach(item => {
                    html += `
                        <div class="order-item">
                            <p><strong>Product ID:</strong> ${item.product_id}</p>
                            <p><strong>Quantity:</strong> ${item.quantity}</p>
                            <p><strong>Price:</strong> ₱${item.price.toFixed(2)}</p>
                        </div>
                    `;
                });

                document.getElementById("order-details").innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                document.getElementById("order-details").innerHTML = "<p>Error loading order.</p>";
            });
    });
    </script>
</body>
</html>
