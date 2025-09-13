<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_management");

header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Check booking_id from POST
if (!isset($_POST['booking_id'])) {
    echo json_encode(["success" => false, "message" => "No booking ID provided."]);
    exit;
}

$bookingId = intval($_POST['booking_id']);

// Update booking status
$sql = "UPDATE bookings SET status = 'Paid' WHERE booking_id = ?";
$stmt = $conn->prepare($sql);

if(!$stmt){
    echo json_encode(["success" => false, "message" => "Query prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $bookingId);
if($stmt->execute()){
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update booking status."]);
}

$stmt->close();
$conn->close();
?>
