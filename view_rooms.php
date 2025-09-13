<?php
// view_rooms.php
include('../db_connect.php');

$sql = "SELECT * FROM rooms ORDER BY room_number ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rooms - Hotel Management</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <h2>Rooms List</h2>
    <a href="add_room.php">➕ Add New Room</a>
    <br><br>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Room ID</th>
            <th>Room Number</th>
            <th>Room Type</th>
            <th>Price per Night</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['room_id'] . "</td>";
                echo "<td>" . $row['room_number'] . "</td>";
                echo "<td>" . $row['room_type'] . "</td>";
                echo "<td>" . $row['price_per_night'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>
                        <a href='edit_room.php?id=" . $row['room_id'] . "'>✏️ Edit</a> | 
                        <a href='delete_room.php?id=" . $row['room_id'] . "' onclick=\"return confirm('Are you sure you want to delete this room?');\">🗑 Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No rooms available.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
