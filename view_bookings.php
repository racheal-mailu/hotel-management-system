<?php
include('db_connect.php');

// Fetch all bookings
$query = "SELECT b.booking_id, b.fullname, b.email, b.check_in, b.check_out, b.status, b.created_at, b.source,
                 r.room_number, r.room_type
          FROM bookings b
          JOIN rooms r ON b.room_id = r.room_id
          ORDER BY b.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Bookings - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .source-admin { color: red; font-weight: bold; }
        .source-customer { color: green; font-weight: bold; }
        .actions a { margin: 0 5px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <h1>All Bookings</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Room</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Source</th> <!-- 👈 New Column -->
            <th>Actions</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?php echo $row['booking_id']; ?></td>
                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo "Room " . $row['room_number'] . " (" . $row['room_type'] . ")"; ?></td>
                <td><?php echo $row['check_in']; ?></td>
                <td><?php echo $row['check_out']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <?php if($row['source'] === 'admin'): ?>
                        <span class="source-admin">Admin</span>
                    <?php else: ?>
                        <span class="source-customer">Customer</span>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a href="edit_booking.php?id=<?php echo $row['booking_id']; ?>">Edit</a> |
                    <a href="delete_booking.php?id=<?php echo $row['booking_id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div style="margin-top: 20px; text-align: center;">
        <a href="admin_dashboard.php" 
           style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">
            Go Back to Admin Dashboard
        </a>
    </div>
</body>
</html>
