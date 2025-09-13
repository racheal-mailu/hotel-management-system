<?php
include('db_connect.php');

// --- Total Bookings ---
$totalBookingsQuery = "SELECT COUNT(*) AS total_bookings FROM bookings";
$totalBookingsResult = mysqli_query($conn, $totalBookingsQuery);
if (!$totalBookingsResult) die("Query failed: " . mysqli_error($conn));
$totalBookings = mysqli_fetch_assoc($totalBookingsResult)['total_bookings'];

// --- Total Orders & Revenue ---
$totalOrdersQuery = "SELECT COUNT(*) AS total_orders, SUM(total) AS total_revenue FROM orders";
$totalOrdersResult = mysqli_query($conn, $totalOrdersQuery);
if (!$totalOrdersResult) die("Query failed: " . mysqli_error($conn));
$totalOrdersRow = mysqli_fetch_assoc($totalOrdersResult);
$totalOrders = $totalOrdersRow['total_orders'];
$totalRevenue = $totalOrdersRow['total_revenue'] ?? 0;

// --- Recent Bookings (last 5) ---
$recentBookingsQuery = "SELECT * FROM bookings ORDER BY booking_id DESC LIMIT 5";
$recentBookingsResult = mysqli_query($conn, $recentBookingsQuery);
if (!$recentBookingsResult) die("Query failed: " . mysqli_error($conn));

// --- Recent Orders (last 5) with customer name ---
$recentOrdersQuery = "
    SELECT o.order_id, o.booking_id, o.total, o.status, o.order_date, b.fullname
    FROM orders o
    LEFT JOIN bookings b ON o.booking_id = b.booking_id
    ORDER BY o.order_id DESC
    LIMIT 5
";
$recentOrdersResult = mysqli_query($conn, $recentOrdersQuery);
if (!$recentOrdersResult) die("Query failed: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports - Hotel Lilies Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f6f8; color: #333; }
        h1 { text-align: center; color: #444; }
        .summary-box { border-radius: 10px; padding: 20px; width: 250px; display: inline-block; margin: 10px; text-align: center; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .summary-box h2 { margin: 0 0 10px 0; color: #555; }
        .summary-box p { font-size: 1.2rem; font-weight: bold; color: #000; margin: 0; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-radius: 5px; overflow: hidden; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #607d8b; color: white; }
        tr:last-child td { border-bottom: none; }
        a.button { padding: 5px 10px; border-radius: 4px; color: white; text-decoration: none; font-size: 0.9rem; }
        .edit-btn { background: #4CAF50; }
        .delete-btn { background: #f44336; }

        .back-btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #607d8b; color: white; text-decoration: none; border-radius: 5px; text-align: center; }
        .back-btn:hover { background-color: #455a64; }
    </style>
</head>
<body>

<h1>Admin Reports</h1>

<!-- Summary Boxes -->
<div class="summary-box">
    <h2>Total Bookings</h2>
    <p><?php echo $totalBookings; ?></p>
</div>
<div class="summary-box">
    <h2>Total Orders</h2>
    <p><?php echo $totalOrders; ?></p>
</div>
<div class="summary-box">
    <h2>Total Revenue</h2>
    <p>Ksh <?php echo number_format($totalRevenue); ?></p>
</div>

<!-- Recent Bookings -->
<h2>Recent Bookings</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Customer Name</th>
        <th>Room ID</th>
        <th>Check-In</th>
        <th>Check-Out</th>
        <th>Status</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($recentBookingsResult)) : ?>
    <tr>
        <td><?php echo $row['booking_id']; ?></td>
        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
        <td><?php echo $row['room_id']; ?></td>
        <td><?php echo $row['check_in']; ?></td>
        <td><?php echo $row['check_out']; ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- Recent Orders -->
<h2>Recent Orders</h2>
<table>
    <tr>
        <th>Order ID</th>
        <th>Customer Name</th>
        <th>Total</th>
        <th>Status</th>
        <th>Order Date</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($recentOrdersResult)) : ?>
    <tr>
        <td><?php echo $row['order_id']; ?></td>
        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
        <td>Ksh <?php echo number_format($row['total']); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
        <td><?php echo $row['order_date']; ?></td>
        <td>
            <a class="button edit-btn" href="edit_order.php?id=<?php echo $row['order_id']; ?>">Edit</a>
        </td>
        <td>
            <a class="button delete-btn" href="delete_order.php?id=<?php echo $row['order_id']; ?>" 
               onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<div style="text-align: center;">
    <a class="back-btn" href="admin_dashboard.php">Go back to Admin Dashboard</a>
</div>

</body>
</html>
