<?php
include('db_connect.php');

if (!isset($_GET['id'])) {
    die("Order ID not provided.");
}

$order_id = intval($_GET['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    $updateQuery = "UPDATE orders SET status=? WHERE order_id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order updated successfully.'); window.location.href='reports.php';</script>";
        exit;
    } else {
        echo "Error updating order: " . $conn->error;
    }
}

// Fetch current order info
$orderQuery = "SELECT o.*, b.fullname FROM orders o LEFT JOIN bookings b ON o.booking_id = b.booking_id WHERE o.order_id=?";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("Order not found.");
$order = $result->fetch_assoc();

// Fetch order items
$itemsQuery = "
    SELECT oi.*, m.name 
    FROM order_items oi 
    JOIN menu_items m ON oi.menu_id = m.menu_id 
    WHERE oi.order_id=?
";
$stmt_items = $conn->prepare($itemsQuery);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Order #<?php echo $order_id; ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f6f8; }
        .container { background: #fff; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        select, button { margin-top: 10px; padding: 8px; width: 100%; border-radius: 5px; border: 1px solid #ccc; }
        button { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
        a { display: block; margin-top: 15px; text-align: center; text-decoration: none; color: #607d8b; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Order #<?php echo $order_id; ?></h2>
    <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['fullname']); ?></p>
    <p><strong>Total:</strong> KSh <?php echo number_format($order['total']); ?></p>

    <form method="POST">
        <label for="status">Order Status</label>
        <select name="status" id="status">
            <option value="Pending" <?php if($order['status'] === 'Pending') echo 'selected'; ?>>Pending</option>
            <option value="Processing" <?php if($order['status'] === 'Processing') echo 'selected'; ?>>Processing</option>
            <option value="Completed" <?php if($order['status'] === 'Completed') echo 'selected'; ?>>Completed</option>
            <option value="Cancelled" <?php if($order['status'] === 'Cancelled') echo 'selected'; ?>>Cancelled</option>
        </select>

        <button type="submit">Update Order</button>
    </form>

    <h3>Order Items</h3>
    <table>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Price (KSh)</th>
            <th>Subtotal (KSh)</th>
        </tr>
        <?php while($item = $result_items->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td><?php echo number_format($item['price'],2); ?></td>
            <td><?php echo number_format($item['price'] * $item['quantity'],2); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="reports.php">Back to Reports</a>
</div>
</body>
</html>
