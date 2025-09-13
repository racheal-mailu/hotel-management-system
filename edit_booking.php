<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit();
}

include('db_connect.php'); 

if(!isset($_GET['id'])){ 
    echo "Booking ID not provided."; 
    exit; 
}
$booking_id = $_GET['id'];

$sql = "SELECT * FROM bookings WHERE booking_id='$booking_id'";
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) == 0){ 
    echo "Booking not found."; 
    exit; 
}
$booking = mysqli_fetch_assoc($result);

if(isset($_POST['update_booking'])){
    $fullname = $_POST['fullname'];
    $room_id_new = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $status = $_POST['status'];
    $room_id_old = $booking['room_id'];

    mysqli_begin_transaction($conn);
    try {
        mysqli_query($conn, "UPDATE bookings 
            SET fullname='$fullname', room_id='$room_id_new', check_in='$check_in', check_out='$check_out', status='$status' 
            WHERE booking_id='$booking_id'");

        if($room_id_new != $room_id_old){
            mysqli_query($conn, "UPDATE rooms SET status='Available' WHERE room_id='$room_id_old'");
            mysqli_query($conn, "UPDATE rooms SET status='Booked' WHERE room_id='$room_id_new'");
        }

        mysqli_commit($conn);
        $msg = "Booking updated successfully!";
        $booking = [
            'fullname'=>$fullname,
            'room_id'=>$room_id_new,
            'check_in'=>$check_in,
            'check_out'=>$check_out,
            'status'=>$status
        ];
    } catch(Exception $e){
        mysqli_rollback($conn);
        $error = "Error updating booking.";
    }
}

$rooms_query = "SELECT * FROM rooms WHERE status='Available' OR room_id='{$booking['room_id']}'";
$rooms_result = mysqli_query($conn, $rooms_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        h2 { text-align: center; margin-bottom: 20px; }
        form { max-width: 800px; margin: auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        .rooms-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .room-card { border: 1px solid #ccc; padding: 10px; width: 120px; text-align: center; border-radius: 8px; transition: transform 0.2s, box-shadow 0.2s; }
        .room-card:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .room-card img { width: 100px; height: 70px; object-fit: cover; border-radius: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #0056b3; }
        .alert { padding: 10px 15px; margin-bottom: 15px; border-radius: 5px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<h2>Edit Booking</h2>

<?php if(isset($msg)) echo "<div class='alert success'>$msg</div>"; ?>
<?php if(isset($error)) echo "<div class='alert error'>$error</div>"; ?>

<form method="POST" action="">
    <label>Full Name:</label>
    <input type="text" name="fullname" value="<?php echo $booking['fullname']; ?>" required>

    <label>Email (read-only):</label>
    <input type="email" name="email" value="<?php echo $booking['email']; ?>" readonly>

    <label>Select Room:</label>
<div class="rooms-container">
    <?php while($room = mysqli_fetch_assoc($rooms_result)){ ?>
        <div class="room-card">
            <img src="uploads/<?php echo $room['image']; ?>" 
                 alt="<?php echo $room['room_number'].' - '.$room['room_type']; ?>">

            <p><?php echo $room['room_number'].' - '.$room['room_type']; ?></p>
            <p>Price: Ksh <?php echo number_format($room['price_per_night'], 2); ?></p>

            <input type="radio" name="room_id" value="<?php echo $room['room_id']; ?>" 
                <?php if($room['room_id']==$booking['room_id']) echo 'checked'; ?> required>
        </div>
    <?php } ?>
</div>



    <label>Check-in Date:</label>
    <input type="date" name="check_in" value="<?php echo $booking['check_in']; ?>" required>

    <label>Check-out Date:</label>
    <input type="date" name="check_out" value="<?php echo $booking['check_out']; ?>" required>

    <label>Status:</label>
    <select name="status" required>
        <option value="Pending" <?php if($booking['status']=='Pending') echo 'selected'; ?>>Pending</option>
        <option value="Confirmed" <?php if($booking['status']=='Confirmed') echo 'selected'; ?>>Confirmed</option>
        <option value="Cancelled" <?php if($booking['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
    </select>

    <button type="submit" name="update_booking">Update Booking</button>
</form>

</body>
</html>
