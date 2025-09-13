<?php
include('db_connect.php');

// Staff session check
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Staff') {
    header("Location: login.php");
    exit;
}

// Fetch bookings for staff (all bookings or assigned bookings)
$bookingsQuery = "SELECT b.booking_id, b.fullname, b.room_id, b.check_in, b.check_out, b.status, p.payment_status 
                  FROM bookings b
                  LEFT JOIN payments p ON b.booking_id = p.booking_id
                  ORDER BY b.check_in ASC";
$bookingsResult = mysqli_query($conn, $bookingsQuery);
if (!$bookingsResult) die("Query failed: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard - Hotel Lilies</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #e8f5e9; }
        a.button { padding: 5px 10px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 3px; }
        a.button:hover { background-color: #0b7dda; }
    </style>
</head>
<body>
    <h1>Staff Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?> | <a href="logout.php">Logout</a></p>

    <h2>All Bookings</h2>
    <table>
        <tr>
            <th>Booking ID</th>
            <th>Customer Name</th>
            <th>Room ID</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Status</th>
            <th>Payment Status</th>
            <th>Actions</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($bookingsResult)) : ?>
        <tr>
            <td><?php echo $row['booking_id']; ?></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo $row['room_id']; ?></td>
            <td><?php echo $row['check_in']; ?></td>
            <td><?php echo $row['check_out']; ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
            <td>
                <a class="button" href="update_booking.php?booking_id=<?php echo $row['booking_id']; ?>">Update Status</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
