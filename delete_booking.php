<?php
// delete_booking.php
include('db_connect.php');

if (!isset($_GET['id'])) {
    die("Booking ID not provided.");
}

$booking_id = intval($_GET['id']);

// Get room_id before deleting booking
$roomQuery = "SELECT room_id FROM bookings WHERE booking_id = $booking_id";
$roomResult = mysqli_query($conn, $roomQuery);

if ($roomResult && mysqli_num_rows($roomResult) > 0) {
    $room = mysqli_fetch_assoc($roomResult);
    $room_id = $room['room_id'];

    // Begin transaction to ensure all deletions succeed together
    mysqli_begin_transaction($conn);

    try {
        // 1. Delete related payments
        mysqli_query($conn, "DELETE FROM payments WHERE booking_id = $booking_id");

        // 2. Delete order items linked via orders
        mysqli_query($conn, "DELETE oi FROM order_items oi
                             JOIN orders o ON oi.order_id = o.order_id
                             WHERE o.booking_id = $booking_id");

        // 3. Delete orders linked to this booking
        mysqli_query($conn, "DELETE FROM orders WHERE booking_id = $booking_id");

        // 4. Delete the booking itself
        mysqli_query($conn, "DELETE FROM bookings WHERE booking_id = $booking_id");

        // 5. Reset room status to Available
        mysqli_query($conn, "UPDATE rooms SET status='Available' WHERE room_id=$room_id");

        // Commit transaction
        mysqli_commit($conn);

        echo "<script>alert('Booking and related data deleted successfully'); window.location.href='view_bookings.php';</script>";
    } catch (Exception $e) {
        // Rollback if any query fails
        mysqli_rollback($conn);
        echo "Error deleting booking: " . $e->getMessage();
    }
} else {
    echo "Booking not found.";
}
?>
