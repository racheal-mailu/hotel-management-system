<?php
include('db_connect.php');
session_start(); // ✅ make sure session is started

// Collect form data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    // Fetch room details
    $room_sql = "SELECT room_type, price_per_night FROM rooms WHERE room_id = '$room_id'";
    $room_result = mysqli_query($conn, $room_sql);
    $room = mysqli_fetch_assoc($room_result);

    $room_type = $room['room_type'];
    $price_per_night = $room['price_per_night'];

    // Insert into bookings table
    $sql = "INSERT INTO bookings (room_id, fullname, email, check_in, check_out) 
            VALUES ('$room_id', '$fullname', '$email', '$check_in', '$check_out')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // ✅ Save booking_id in session
        $_SESSION['booking_id'] = mysqli_insert_id($conn);

        $status = "success";
        $message = "Thank you <strong>$fullname</strong>! Your booking has been confirmed.";

        // -------- Send Email Confirmation -------- //
        $subject = "Hotel Lilies - Booking Confirmation";
        $email_message = "
        Dear $fullname,\n\n
        Thank you for booking with Hotel Lilies.\n
        Your stay has been reserved as follows:\n
        Room: $room_type\n
        Price per Night: Ksh $price_per_night\n
        Check-in Date: $check_in\n
        Check-out Date: $check_out\n\n
        We look forward to hosting you!\n\n
        Regards,\n
        Hotel Lilies Team
        ";

        $headers = "From: Hotel Lilies <noreply@hotellilies.com>";
        @mail($email, $subject, $email_message, $headers);

    } else {
        $status = "error";
        $message = "Something went wrong: " . mysqli_error($conn);
    }
}
?>
