<?php
// add_room.php
include('db_connect.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price_per_night = $_POST['price_per_night'];
    $status = $_POST['status'];

    $sql = "INSERT INTO rooms (room_number, room_type, price_per_night, status)
            VALUES ('$room_number', '$room_type', '$price_per_night', '$status')";

    if ($conn->query($sql) === TRUE) {
        $message = "✅ Room added successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room - Hotel Management</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <h2>Add New Room</h2>

    <?php if (!empty($message)) echo "<p>$message</p>"; ?>

    <form method="post" action="">
        <label>Room Number:</label><br>
        <input type="text" name="room_number" required><br><br>

        <label>Room Type:</label><br>
        <select name="room_type" required>
            <option value="Single">Single</option>
            <option value="Double">Double</option>
            <option value="Suite">Suite</option>
        </select><br><br>

        <label>Price per Night:</label><br>
        <input type="number" step="0.01" name="price_per_night" required><br><br>

        <label>Status:</label><br>
        <select name="status" required>
            <option value="Available">Available</option>
            <option value="Booked">Booked</option>
            <option value="Maintenance">Maintenance</option>
        </select><br><br>

        <input type="submit" value="Add Room">
    </form>
</body>
</html>
