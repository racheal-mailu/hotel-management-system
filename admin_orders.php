<?php
session_start();
include('db_connect.php');

// Optional: Admin authentication check
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header("Location: admin_login.php");
//     exit;
// }

// Handle status updates
if (isset($_GET['update_status'], $_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $new_status = $_GET['update_status'];

    $update_sql = "UPDATE orders SET status='$new_status' WHERE order_id=$order_id";
    if (mysqli_query($conn, $update_sql)) {
        header("Location: admin_orders.php");
        exit;
    } else {
        echo "Error updating status: " . mysqli_error($conn);
    }
}

// Handle order deletion
if (isset($_GET['delete_order_id'])) {
    $delete_order_id = intval($_GET['delete_order_id']);
    $delete_sql = "DELETE FROM orders WHERE order_id=$delete_order_id";
    if (mysqli_query($conn, $delete_sql)) {
        header("Location: admin_orders.php");
        exit;
    } else {
        echo "Error deleting order: " . mysqli_error($conn);
    }
}

// Fetch orders with booking info
$sql_orders = "
    SELECT o.*, b.fullname AS customer_name, b.room_id
    FROM orders o
    LEFT JOIN bookings b ON o.booking_id = b.booking_id
    ORDER BY o.order_date DESC
";

$orders_result = mysqli_query($conn, $sql_orders);

if (!$orders_result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Orders</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f2f2f2;
        }
        .status-btn, .delete-btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }
        .pending { background-color: orange; }
        .completed { background-color: green; }
        .delete-btn { background-color: red; }
    </style>
    <script>
        function confirmDelete(orderId) {
            if (confirm("Are you sure you want to delete this order?")) {
                window.location.href = "admin_orders.php?delete_order_id=" + orderId;
            }
        }
    </script>
</head>
<body>
    <h1>Orders Management</h1>

    <?php if (mysqli_num_rows($orders_result) > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Room</th>
                <th>Total</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
            <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($order['room_id'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($order['total']); ?></td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                    <td>
                        <?php if ($order['status'] !== 'completed'): ?>
                            <a href="admin_orders.php?update_status=completed&order_id=<?php echo $order['order_id']; ?>" class="status-btn completed">Mark Completed</a>
                        <?php else: ?>
                            <span class="status-btn completed">Completed</span>
                        <?php endif; ?>
                        <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $order['order_id']; ?>)" class="delete-btn">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>
</body>
</html>
