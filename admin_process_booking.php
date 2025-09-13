<?php
include('db_connect.php');
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $room_id   = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
    $fullname  = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $email     = isset($_POST['email']) ? trim($_POST['email']) : '';
    $check_in  = isset($_POST['check_in']) ? $_POST['check_in'] : '';
    $check_out = isset($_POST['check_out']) ? $_POST['check_out'] : '';

    $errors = [];

    // Validation
    if(!$fullname) $errors[] = "Full name is required.";
    if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if(!$check_in) $errors[] = "Check-in date is required.";
    if(!$check_out) $errors[] = "Check-out date is required.";
    if($check_in && $check_out && $check_in >= $check_out) $errors[] = "Check-out must be after check-in.";
    if($room_id <= 0) $errors[] = "Invalid room selection.";

    if(empty($errors)){
        // Check if room is available
        $stmt = $conn->prepare("SELECT status, price_per_night, room_type, room_number 
                                FROM rooms WHERE room_id = ?");
        if(!$stmt){
            die("Prepare failed: ".$conn->error);
        }
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();
        $stmt->close();

        if(!$room){
            $errors[] = "Selected room does not exist.";
        } elseif($room['status'] != 'Available'){
            $errors[] = "Selected room is no longer available.";
        } else {
            // Insert booking with source='admin'
            $stmt = $conn->prepare("INSERT INTO bookings 
                (room_id, fullname, email, check_in, check_out, status, created_at, source) 
                VALUES (?, ?, ?, ?, ?, 'Booked', NOW(), 'admin')");
            if(!$stmt){
                die("Prepare failed: ".$conn->error);
            }
            $stmt->bind_param("issss", $room_id, $fullname, $email, $check_in, $check_out);

            if($stmt->execute()){
                // Update room status
                $update = $conn->prepare("UPDATE rooms SET status='Booked' WHERE room_id=?");
                if(!$update){
                    die("Prepare failed: ".$conn->error);
                }
                $update->bind_param("i", $room_id);
                $update->execute();
                $update->close();

                // Success message
                echo "<div style='text-align:center; margin-top:50px;'>";
                echo "<h2 style='color:green;'>Booking Added Successfully (Admin)</h2>";
                echo "<p><strong>Customer Name:</strong> {$fullname}</p>";
                echo "<p><strong>Room:</strong> {$room['room_type']} (Room {$room['room_number']})</p>";
                echo "<p><strong>Price per Night:</strong> Ksh {$room['price_per_night']}</p>";
                echo "<p><strong>Check-in:</strong> {$check_in}</p>";
                echo "<p><strong>Check-out:</strong> {$check_out}</p>";
                echo "<a href='admin_dashboard.php' style='display:inline-block;margin-top:20px;padding:10px 20px;background:#007bff;color:white;border-radius:5px;text-decoration:none;'>Back to Admin Dashboard</a>";
                echo "</div>";
            } else {
                $errors[] = "Database error: ".$stmt->error;
            }
            $stmt->close();
        }
    }

    if(!empty($errors)){
        echo "<div style='color:red; text-align:center; margin-top:30px;'>";
        echo "<h3>Errors occurred:</h3><ul style='list-style:none; padding:0;'>";
        foreach($errors as $e) echo "<li>{$e}</li>";
        echo "</ul>";
        echo "<a href='javascript:history.back()' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#ff4444;color:white;border-radius:5px;text-decoration:none;'>Go Back</a>";
        echo "</div>";
    }

} else {
    echo "<p style='text-align:center; color:red;'>Invalid request method.</p>";
}
?>
