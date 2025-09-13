<?php
session_start();
include('db_connect.php');

// Staff session check
if(!isset($_SESSION['staff_logged_in']) || $_SESSION['role'] !== 'Staff'){
    header("Location: staff_login.php");
    exit;
}

// Fetch all bookings
$query = "SELECT b.booking_id, b.fullname, b.status AS booking_status, 
                 COALESCE(p.status, 'Pending') AS payment_status
          FROM bookings b
          LEFT JOIN payments p ON b.booking_id = p.booking_id
          ORDER BY b.booking_id DESC";

$result = mysqli_query($conn, $query);

// Check for errors
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff - All Bookings</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #009688; color: white; }
        a.button { padding: 5px 10px; background: #2575fc; color: white; text-decoration: none; border-radius: 5px; }
        a.back { padding: 8px 15px; background: #009688; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<h2 style="text-align:center;">All Bookings</h2>

<?php if(mysqli_num_rows($result) > 0): ?>
<table>
    <tr>
        <th>Booking ID</th>
        <th>Customer Name</th>
        <th>Booking Status</th>
        <th>Payment Status</th>
        <th>Action</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?php echo $row['booking_id']; ?></td>
        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
        <td><?php echo $row['booking_status']; ?></td>
        <td><?php echo $row['payment_status']; ?></td>
        <td>
            <a class="button" href="update_booking.php?booking_id=<?php echo $row['booking_id']; ?>">Update</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;">No bookings found.</p>
<?php endif; ?>

<div style="text-align:center;">
    <a class="back" href="staff_portal.php">Back to Staff Portal</a>
</div>

</body>
</html>
