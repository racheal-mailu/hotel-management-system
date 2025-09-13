<?php
// edit_room.php
include('db_connect.php');

if (!isset($_GET['id'])) {
    die("Room ID not provided.");
}

$room_id = $_GET['id'];

// Fetch current room details
$sql = "SELECT * FROM rooms WHERE room_id = $room_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Room not found.");
}

$room = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price_per_night = $_POST['price_per_night'];
    $status = $_POST['status'];

    $update_sql = "UPDATE rooms 
                   SET room_number='$room_number', room_type='$room_type', price_per_night='$price_per_night', status='$status'
                   WHERE room_id=$room_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Room updated successfully'); window.location.href='manage_rooms.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room - Hotel Management</title>
</head>
<body>
    <h2>Edit Room</h2>
    <form method="POST" action="">
        <label>Room Number:</label><br>
        <input type="text" name="room_number" value="<?php echo $room['room_number']; ?>" required><br><br>

        <label>Room Type:</label><br>
        <input type="text" name="room_type" value="<?php echo $room['room_type']; ?>" required><br><br>

        <label>Price per Night:</label><br>
        <input type="number" step="0.01" name="price_per_night" value="<?php echo $room['price_per_night']; ?>" required><br><br>

        <label>Status:</label><br>
        <select name="status" required>
            <option value="Available" <?php if ($room['status']=="Available") echo "selected"; ?>>Available</option>
            <option value="Booked" <?php if ($room['status']=="Booked") echo "selected"; ?>>Booked</option>
            <option value="Maintenance" <?php if ($room['status']=="Maintenance") echo "selected"; ?>>Maintenance</option>
        </select><br><br>

        <button type="submit">Update Room</button>
        <a href="manage_rooms.php">Cancel</a>
    </form>
    <div style="margin-top: 20px; text-align: center;">
    <a href="admin_dashboard.php" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
        Go back to Admin Dashboard
    </a>
</div>

</body>
</html>
