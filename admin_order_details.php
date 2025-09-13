<?php
session_start();
include('db_connect.php');

// (Optional) Admin authentication check
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header("Location: admin_login.php");
//     exit;
// }

// Check order_id
if (!isset($_GET['order_id'])) {
    header("Location: admin_orders.php");
    exit;
}

$order_id = intval($_GET['order_id']);

// Handle status update
if (isset($_POST['update_status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $allowed = ['Pending', 'Approved', 'Completed', 'Cancelled'];

    if (in_array($new_status, $allowed)) {
        $sql_update = "UPDATE orders SET status = '$new_status' WHERE order_id = '$order_id'";
        if (mysqli_query($conn, $sql_update)) {
            $_SESSION['msg'] = "✅ Order status updated successfully.";
        } else {
            $_SESSION['msg'] = "❌ Error updating order status: " . mysqli_error($conn);
        }
        header("Location: admin_order_details.php?order_id=$order_id");
        exit;
    }
}

// Fetch order info
$sql_order = "SELECT o.*, b.customer_name, b.room_number 
              FROM orders o
              LEFT JOIN bookings b ON o.booking_id = b.booking_id
              WHERE o.order_id = $order_id";
$order_result = mysqli_query($conn, $sql_order);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header("Location: admin_orders.php?msg=❌ Order not found");
    exit;
}

// Fetch order items
$sql_items = "SELECT oi.*, m.name 
              FROM order_items oi
              JOIN menu_items m ON oi.menu_id = m.menu_id
              WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $sql_items);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Order #<?php echo $order_id; ?> Details | Hotel Lilies</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; margin: 0; padding: 0; }
        header { background: linear-gradient(90deg, #343a40, #007bff); color: white; padding: 20px; text-align: center; }
        .container { max-width: 900px; margin: 30px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #343a40; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        table th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .status { font-weight: bold; color: #007bff; }
        .btn { padding: 8px 14px; border-radius: 5px; text-decoration: none; color: white; margin: 4px; display: inline-block; }
        .btn-back { background: #6c757d; }
        .btn-update { background: #007bff; border: none; cursor: pointer; }
        .btn-update:hover { opacity: 0.85; }
        select { padding: 6px; border-radius: 5px; border: 1px solid #ccc; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
<header>
    <h1>Hotel Lilies - Admin Panel</h1>
    <p>Order #<?php echo $order_id; ?> Details</p>
</header>

<div class="container">

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert <?php echo (strpos($_SESSION['msg'], '✅') !== false) ? 'success' : 'error'; ?>">
            <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
        </div>
    <?php endif; ?>

    <h2>Order Information</h2>
    <p><strong>Customer:</strong> <?php echo $order['customer_name'] ?? 'N/A'; ?></p>
    <p><strong>Room:</strong> <?php echo $order['room_number'] ?? 'N/A'; ?></p>
    <p><strong>Date:</strong> <?php echo $order['order_date']; ?></p>
    <p><strong>Total:</strong> Ksh <?php echo number_format($order['total'], 2); ?></p>
    <p><strong>Status:</strong> <span class="status"><?php echo $order['status']; ?></span></p>

    <h2>Order Items</h2>
    <table>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Price (Ksh)</th>
            <th>Subtotal (Ksh)</th>
        </tr>
        <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>Update Status</h2>
    <form method="post">
        <select name="status">
            <option value="Pending" <?php if ($order['status']=='Pending') echo 'selected'; ?>>Pending</option>
            <option value="Approved" <?php if ($order['status']=='Approved') echo 'selected'; ?>>Approved</option>
            <option value="Completed" <?php if ($order['status']=='Completed') echo 'selected'; ?>>Completed</option>
            <option value="Cancelled" <?php if ($order['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
        </select>
        <button type="submit" name="update_status" class="btn btn-update">Update</button>
    </form>

    <p style="margin-top:20px;">
        <a href="admin_orders.php" class="btn btn-back">⬅️ Back to Orders</a>
    </p>
</div>
</body>
</html>
