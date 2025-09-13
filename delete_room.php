<?php
// delete_booking.php
include('db_connect.php');

if (!isset($_GET['id'])) {
    die("Booking ID not provided.");
}

$booking_id = intval($_GET['id']);

// 1. Get the room_id linked to this booking
$sql = "SELECT room_id FROM bookings WHERE booking_id = $booking_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $room_id = $row['room_id'];

    // 2. Delete the booking
    $deleteBooking = "DELETE FROM bookings WHERE booking_id = $booking_id";
    if ($conn->query($deleteBooking) === TRUE) {
        // 3. Reset room status to 'Available'
        $updateRoom = "UPDATE rooms SET status = 'Available' WHERE room_id = $room_id";
        $conn->query($updateRoom);

        echo "<script>alert('Booking deleted successfully'); window.location.href='view_bookings.php';</script>";
    } else {
        echo "Error deleting booking: " . $conn->error;
    }
} else {
    echo "Booking not found.";
}
?>
