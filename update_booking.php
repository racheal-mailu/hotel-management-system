<?php
include('db_connect.php');
session_start();

// Staff session check
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['role'] !== 'Staff') {
    header("Location: staff_login.php");
    exit;
}

// Check if booking_id is passed
if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
    $noBooking = true;
} else {
    $booking_id = intval($_GET['booking_id']);
}

// Handle form submission
if (isset($_POST['update_booking'])) {
    $status = $_POST['status'];
    $payment_status = $_POST['payment_status'];

    // Update booking status
    $updateBookingQuery = "UPDATE bookings SET status='$status' WHERE booking_id=$booking_id";
    mysqli_query($conn, $updateBookingQuery) or die("Booking update failed: " . mysqli_error($conn));

    // Update payment status
    $updatePaymentQuery = "UPDATE payments SET status='$payment_status' WHERE booking_id=$booking_id";
    mysqli_query($conn, $updatePaymentQuery) or die("Payment update failed: " . mysqli_error($conn));

    header("Location: staff_bookings.php");
    exit;
}

// Fetch current booking and payment details if ID exists
if (!isset($noBooking)) {
    $bookingQuery = "SELECT b.booking_id, b.fullname, b.status AS booking_status, 
                            COALESCE(p.status, 'Pending') AS payment_status
                     FROM bookings b
                     LEFT JOIN payments p ON b.booking_id = p.booking_id
                     WHERE b.booking_id=$booking_id";
    $result = mysqli_query($conn, $bookingQuery);

    if (!$result || mysqli_num_rows($result) == 0) {
        $noBooking = true;
    } else {
        $row = mysqli_fetch_assoc($result);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Booking - Staff</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        form { max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; background: white; }
        label { display: block; margin-top: 10px; }
        select { width: 100%; padding: 5px; margin-top: 5px; }
        input[type="submit"] { margin-top: 20px; padding: 10px; width: 100%; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
        a { display: block; text-align: center; margin-top: 10px; text-decoration: none; color: #2196F3; }
        .message { text-align:center; padding:20px; background:#fff; max-width:400px; margin: 50px auto; border-radius:5px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<?php if(isset($noBooking) && $noBooking): ?>
    <div class="message">
        <h2>No Booking Selected</h2>
        <p>Please select a booking from <a href="staff_bookings.php">the bookings list</a>.</p>
    </div>
<?php else: ?>
    <h2 style="text-align:center;">Update Booking</h2>
    <form method="POST">
        <label>Customer Name:</label>
        <input type="text" value="<?php echo htmlspecialchars($row['fullname']); ?>" disabled>

        <label>Booking Status:</label>
        <select name="status">
            <option value="Pending" <?php if($row['booking_status']=='Pending') echo 'selected'; ?>>Pending</option>
            <option value="Confirmed" <?php if($row['booking_status']=='Confirmed') echo 'selected'; ?>>Confirmed</option>
            <option value="Checked-in" <?php if($row['booking_status']=='Checked-in') echo 'selected'; ?>>Checked-in</option>
            <option value="Checked-out" <?php if($row['booking_status']=='Checked-out') echo 'selected'; ?>>Checked-out</option>
            <option value="Cancelled" <?php if($row['booking_status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
        </select>

        <label>Payment Status:</label>
        <select name="payment_status">
            <option value="Pending" <?php if($row['payment_status']=='Pending') echo 'selected'; ?>>Pending</option>
            <option value="Paid" <?php if($row['payment_status']=='Paid') echo 'selected'; ?>>Paid</option>
            <option value="Refunded" <?php if($row['payment_status']=='Refunded') echo 'selected'; ?>>Refunded</option>
        </select>

        <input type="submit" name="update_booking" value="Update Booking">
        <a href="staff_bookings.php">Back to Bookings List</a>
    </form>
<?php endif; ?>
</body>
</html>
