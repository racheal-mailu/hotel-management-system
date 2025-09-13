<?php
session_start();
include('db_connect.php');

// Ensure the customer has a booking session
if (!isset($_SESSION['booking_id'])) {
    echo "<p style='text-align:center; color:red;'>You must make a booking before viewing order details.</p>";
    echo "<p style='text-align:center;'><a href='rooms.php'>Make a Booking</a></p>";
    exit;
}

$booking_id = $_SESSION['booking_id'];

// Cancel request
if (isset($_GET['cancel']) && isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $sql_cancel = "UPDATE orders 
                   SET status = 'Cancelled' 
                   WHERE order_id = '$order_id' AND booking_id = '$booking_id' AND status = 'Pending'";
    if (mysqli_query($conn, $sql_cancel)) {
        echo "<script>alert('Order has been cancelled successfully.'); window.location='my_orders.php';</script>";
        exit;
    } else {
        echo "<script>alert('Unable to cancel order. Please try again.');</script>";
    }
}

// Get order_id from URL
if (!isset($_GET['order_id'])) {
    echo "<p style='text-align:center; color:red;'>Invalid request.</p>";
    exit;
}

$order_id = intval($_GET['order_id']);

// Fetch the order to ensure it belongs to this booking
$sql = "SELECT * FROM orders WHERE order_id = '$order_id' AND booking_id = '$booking_id'";
$order_result = mysqli_query($conn, $sql);

if (mysqli_num_rows($order_result) == 0) {
    echo "<p style='text-align:center; color:red;'>Order not found or you don't have permission to view it.</p>";
    exit;
}

$order = mysqli_fetch_assoc($order_result);

// Fetch order items
$sql_items = "SELECT oi.*, m.item_name 
              FROM order_items oi
              JOIN menu_items m ON oi.menu_id = m.menu_id
              WHERE oi.order_id = '$order_id'";
$items_result = mysqli_query($conn, $sql_items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Hotel Lilies</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; margin: 0; padding: 0; }
        header { background: linear-gradient(90deg, #007bff, #00c6ff); color: white; padding: 20px; text-align: center; }
        .container { max-width: 800px; margin: 40px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
        h2 { color: #007bff; text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        table th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .total { text-align: right; font-size: 18px; margin-top: 15px; font-weight: bold; }
        .status { font-weight: bold; color: #007bff; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 18px; border-radius: 6px; text-decoration: none; transition: 0.3s; }
        .btn-back { background: #007bff; color: white; }
        .btn-back:hover { background: #0056b3; }
        .btn-cancel { background: #dc3545; color: white; }
        .btn-cancel:hover { background: #a71d2a; }
    </style>
</head>
<body>
<header>
    <h1>Hotel Lilies</h1>
    <p>Order Details</p>
</header>

<div class="container">
    <h2>Order #<?php echo $order['order_id']; ?></h2>
    <p><strong>Date:</strong> <?php echo $order['order_date']; ?></p>
    <p><strong>Status:</strong> <span class="status"><?php echo $order['status']; ?></span></p>

    <?php if (mysqli_num_rows($items_result) > 0): ?>
        <table>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price (Ksh)</th>
                <th>Subtotal (Ksh)</th>
            </tr>
            <?php 
            $grand_total = 0;
            while ($item = mysqli_fetch_assoc($items_result)): 
                $subtotal = $item['quantity'] * $item['price'];
                $grand_total += $subtotal;
            ?>
                <tr>
                    <td><?php echo $item['item_name']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo number_format($subtotal, 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <p class="total">Total: Ksh <?php echo number_format($grand_total, 2); ?></p>
    <?php else: ?>
        <p style="text-align:center; color:#666;">No items found in this order.</p>
    <?php endif; ?>

    <div style="text-align:center;">
        <a href="my_orders.php" class="btn btn-back">⬅ Back to My Orders</a>
        <a href="menu.php" class="btn btn-back">🍽️ Order More</a>
        <?php if ($order['status'] == 'Pending'): ?>
            <a href="order_details.php?cancel=1&order_id=<?php echo $order['order_id']; ?>" 
               class="btn btn-cancel" 
               onclick="return confirm('Are you sure you want to cancel this order?');">
               ❌ Cancel Order
            </a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
