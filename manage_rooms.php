<?php
include('db_connect.php');

// Handle filter
$filter = isset($_GET['status']) ? $_GET['status'] : '';

// Fetch all rooms with latest booking info
$roomsQuery = "
    SELECT r.room_id, r.room_number, r.room_type, r.price_per_night, r.status, 
           b.fullname, b.email, b.check_in, b.check_out
    FROM rooms r
    LEFT JOIN (
        SELECT b1.room_id, b1.fullname, b1.email, b1.check_in, b1.check_out
        FROM bookings b1
        INNER JOIN (
            SELECT room_id, MAX(booking_id) AS latest_booking
            FROM bookings
            GROUP BY room_id
        ) b2 ON b1.room_id = b2.room_id AND b1.booking_id = b2.latest_booking
    ) b ON r.room_id = b.room_id
";

if ($filter) {
    $roomsQuery .= " WHERE r.status = '".mysqli_real_escape_string($conn, $filter)."'";
}

$roomsQuery .= " ORDER BY r.room_number ASC";
$roomsResult = mysqli_query($conn, $roomsQuery);
if (!$roomsResult) die("Query failed: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Rooms - Hotel Lilies Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-available { color: green; font-weight: bold; }
        .status-booked { color: red; font-weight: bold; }
        .status-maintenance { color: orange; font-weight: bold; }
        .action a { margin-right: 5px; }
        .filters { margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Customers & Booked Rooms</h1>

    <div class="filters">
        <form method="GET" action="">
            <label>Filter by Status:</label>
            <select name="status" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="Available" <?php if($filter=="Available") echo "selected"; ?>>Available</option>
                <option value="Booked" <?php if($filter=="Booked") echo "selected"; ?>>Booked</option>
                <option value="Maintenance" <?php if($filter=="Maintenance") echo "selected"; ?>>Maintenance</option>
            </select>
        </form>
    </div>

    <table>
        <tr>
            <th>Room Number</th>
            <th>Room Type</th>
            <th>Status</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Total Cost</th>
            <th>Actions</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($roomsResult)) : ?>
        <?php
            $totalCost = "";
            if ($row['status'] == 'Booked' && $row['check_in'] && $row['check_out']) {
                $checkIn = new DateTime($row['check_in']);
                $checkOut = new DateTime($row['check_out']);
                $nights = $checkIn->diff($checkOut)->days;
                $totalCost = "KES " . number_format($nights * $row['price_per_night']);
            }
        ?>
        <tr>
            <td><?php echo $row['room_number']; ?></td>
            <td><?php echo $row['room_type']; ?></td>
            <td>
                <?php 
                if ($row['status'] == 'Available') {
                    echo '<span class="status-available">Available</span>';
                } elseif ($row['status'] == 'Booked') {
                    echo '<span class="status-booked">Booked</span>';
                } else {
                    echo '<span class="status-maintenance">Maintenance</span>';
                }
                ?>
            </td>
            <td><?php echo ($row['status'] == 'Booked') ? htmlspecialchars($row['fullname']) : ''; ?></td>
            <td><?php echo ($row['status'] == 'Booked') ? htmlspecialchars($row['email']) : ''; ?></td>
            <td><?php echo ($row['status'] == 'Booked') ? htmlspecialchars($row['check_in']) : ''; ?></td>
            <td><?php echo ($row['status'] == 'Booked') ? htmlspecialchars($row['check_out']) : ''; ?></td>
            <td><?php echo ($row['status'] == 'Booked') ? $totalCost : ''; ?></td>
            <td class="action">
                <a href="edit_room.php?id=<?php echo $row['room_id']; ?>">Edit</a>
               <a href="delete_room.php?id=<?php echo $row['room_id']; ?>" onclick="return confirm('Are you sure you want to delete this room?');">Delete</a>

        </tr>
        <?php endwhile; ?>
    </table>
    <div style="margin-top: 20px; text-align: center;">
    <a href="admin_dashboard.php" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
        Go back to Admin Dashboard
    </a>
</div>

</body>
</html>
