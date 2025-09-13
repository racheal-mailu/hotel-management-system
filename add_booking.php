<?php
include('db_connect.php');

$message = "";

// Handle form submission
if(isset($_POST['add_booking'])){
    $fullname   = $_POST['fullname'];
    $email      = $_POST['email'];
    $room_id    = $_POST['room_id'];
    $check_in   = $_POST['check_in'];
    $check_out  = $_POST['check_out'];
    $status     = 'Booked';
    $created_at = date('Y-m-d H:i:s');
    $source     = 'admin'; // 👈 mark booking as created by admin

    // Insert into bookings
    $insertQuery = "INSERT INTO bookings 
        (room_id, check_in, check_out, status, created_at, fullname, email, source) 
        VALUES 
        ('$room_id', '$check_in', '$check_out', '$status', '$created_at', '$fullname', '$email', '$source')";
    
    $result = mysqli_query($conn, $insertQuery);

    if($result){
        // Update room status
        mysqli_query($conn, "UPDATE rooms SET status='Booked' WHERE room_id='$room_id'");
        $message = "Booking added successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

// Fetch available rooms
$roomsQuery = "SELECT * FROM rooms WHERE status='Available'";
$roomsResult = mysqli_query($conn, $roomsQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Booking - Hotel Lilies Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; margin-top: 20px; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; }
        .message { margin-top: 20px; color: green; }
    </style>
</head>
<body>
    
    <h1>Add Booking (Admin)</h1>

    <?php if($message) echo "<div class='message'>$message</div>"; ?>

    <form method="POST" action="">
        <label for="fullname">Customer Full Name:</label>
        <input type="text" name="fullname" required>

        <label for="email">Customer Email:</label>
        <input type="email" name="email" required>

        <label for="room_id">Select Room:</label>
        <select name="room_id" required>
            <option value="">-- Select Room --</option>
            <?php while($room = mysqli_fetch_assoc($roomsResult)) : ?>
                <option value="<?php echo $room['room_id']; ?>">
                    <?php echo $room['room_number'] . " - " . $room['room_type']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="check_in">Check-In Date:</label>
        <input type="date" name="check_in" required>

        <label for="check_out">Check-Out Date:</label>
        <input type="date" name="check_out" required>

        <button type="submit" name="add_booking">Add Booking</button>
    </form>
    <div style="margin-top: 20px; text-align: center;">
        <a href="admin_dashboard.php" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
            Go back to Admin Dashboard
        </a>
    </div>

</body>
</html>
