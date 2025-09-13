<?php
include('db_connect.php');

$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

// Fetch selected room info
$room = null;
if($room_id > 0){
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Your Room - Hotel Lilies</title>
<style>
body {font-family: Arial; background:#f4f7fa; margin:0; padding:0;}
header {background:#007bff;color:white;padding:20px;text-align:center;}
.container {max-width:600px;margin:50px auto;background:white;padding:30px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
input, select {width:100%;padding:10px;margin:8px 0;border-radius:5px;border:1px solid #ccc;}
button {padding:12px 20px;background:#007bff;color:white;border:none;border-radius:5px;cursor:pointer;}
button:hover {background:#0056b3;}
label {font-weight:bold;}
</style>
</head>
<body>

<header>
    <h1>Hotel Lilies</h1>
    <p>Booking Form</p>
</header>

<div class="container">
<?php if(!$room): ?>
    <p style="color:red;">No room selected. Please pick a room first.</p>
    <a href="rooms.php">Back to Rooms</a>
<?php else: ?>
    <form method="POST" action="process_booking.php">
        <p><strong>Selected Room:</strong> <?php echo htmlspecialchars($room['room_type']); ?> (Room <?php echo $room['room_number']; ?>)</p>
        <p><strong>Price per Night:</strong> Ksh <?php echo $room['price_per_night']; ?></p>

        <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">

        <label>Full Name</label>
        <input type="text" name="fullname" placeholder="Enter your full name" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Check-in Date</label>
        <input type="date" name="check_in" required>

        <label>Check-out Date</label>
        <input type="date" name="check_out" required>

        <button type="submit">Confirm Booking</button>
    </form>
<?php endif; ?>
<div style="margin-top: 20px; text-align: center;">
    <a href="customer_portal.php" 
       style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">
        Go Back to Customer Portal
    </a>
</div>

</div>

</body>
</html>
