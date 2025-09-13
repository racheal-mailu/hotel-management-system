<?php
include('db_connect.php');

// Get room details if passed from rooms.php
$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : '';
$room_type = isset($_GET['type']) ? $_GET['type'] : '';
$price_per_night = isset($_GET['price']) ? $_GET['price'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room - Hotel Lilies</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #f1f3f6);
            margin: 0;
            padding: 0;
        }
        header {
            background: linear-gradient(90deg, #007bff, #00c6ff);
            color: white;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .container {
            max-width: 650px;
            margin: 40px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }
        h2 {
            color: #007bff;
            text-align: center;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-top: 12px;
            font-weight: 600;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #bbb;
            font-size: 15px;
        }
        input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0,123,255,0.4);
        }
        button {
            margin-top: 25px;
            background: linear-gradient(90deg, #007bff, #00c6ff);
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            transition: 0.3s ease;
        }
        button:hover {
            background: linear-gradient(90deg, #0056b3, #009acd);
        }
        .room-summary {
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .room-summary h3 {
            margin: 0 0 10px 0;
            color: #444;
        }
    </style>
</head>
<body>

<header>
    <h1>Hotel Lilies</h1>
    <p>Book Your Stay</p>
</header>

<div class="container">
    <h2>Booking Form</h2>

    <div class="room-summary">
        <h3>Selected Room</h3>
        <p><strong>Type:</strong> <?php echo $room_type ?: 'Not specified'; ?></p>
        <p><strong>Price per Night:</strong> Ksh <?php echo $price_per_night ?: '0'; ?></p>
    </div>

    <form action="process_booking.php" method="POST">
        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">

        <label>Full Name</label>
        <input type="text" name="fullname" placeholder="Enter your full name" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email address" required>

        <label>Check-in Date</label>
        <input type="date" name="check_in" required>

        <label>Check-out Date</label>
        <input type="date" name="check_out" required>

        <button type="submit">Confirm Booking</button>
    </form>
</div>

</body>
</html>
