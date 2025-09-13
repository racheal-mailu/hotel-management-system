<?php
session_start();
include('db_connect.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Portal - Hotel Lilies</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background: #f0f4f8;
        color: #333;
    }
    header {
        background: #888888;
        color: white;
        padding: 25px 0;
        text-align: center;
    }
    header h1 {
        margin: 0;
        font-size: 2.5rem;
    }
    .portal-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        max-width: 1000px;
        margin: 50px auto;
        padding: 0 20px;
    }
    .tile {
        border-radius: 15px;
        color: white;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
        font-weight: bold;
        font-size: 1.2rem;
        position: relative;
    }
    .tile:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .tile h2 {
        margin: 10px 0 5px;
        font-size: 1.5rem;
    }
    .tile p {
        margin: 0;
        font-size: 0.9rem;
        font-style: italic;
    }
    .note {
        font-size: 0.8rem;
        font-style: normal;
        margin-top: 8px;
        color: #e0f7fa;
    }

    /* Tile colors */
    .rooms { background: #4db6ac; }
    .menu { background: #ba68c8; }
    .payments { background: #ff8a65; }
    .chatbot { background: #64b5f6; }

    .emoji {
        font-size: 3rem;
        margin-bottom: 10px;
    }
</style>
</head>
<body>

<header>
    <h1>Customer Portal</h1>
    <p>Welcome! Explore our rooms, menu, and services.</p>
</header>

<div class="portal-options">
    <!-- View Available Rooms -->
    <div class="tile rooms" onclick="location.href='rooms.php'">
        <div class="emoji">🏢</div>
        <h2>View Available Rooms</h2>
        <p>See all rooms available for booking</p>
        <div class="note">Pick a room from the slideshow to proceed to booking</div>
    </div>

    <!-- View Restaurant Menu -->
    <div class="tile menu" onclick="location.href='menu.php'">
        <div class="emoji">🍽️</div>
        <h2>View Restaurant Menu</h2>
        <p>Check our delicious dishes and prices</p>
    </div>

    <!-- View Payments -->
    <div class="tile payments" 
         onclick="location.href='payments.php<?php echo isset($_SESSION['customer_email']) ? '?email=' . urlencode($_SESSION['customer_email']) : ''; ?>'">
        <div class="emoji">💳</div>
        <h2>View Payments</h2>
        <p>Pay for your bookings quickly and securely</p>
    </div>

    <!-- Hotel Assistant -->
    <div class="tile chatbot" onclick="location.href='hotel_assistant.php'">
        <div class="emoji">💬</div>
        <h2>Hotel Assistant</h2>
        <p>Chat with our assistant for help and FAQs</p>
    </div>
<!-- Customer Dashboard Tile -->
<div class="tile dashboard" style="background:#81c784; cursor:default; padding:20px; text-align:left;">
    <div class="emoji">📊</div>
    <h2 style="text-align:center; font-size:1.2rem;">My Dashboard</h2>
    <?php include('customer_dashboard.php'); ?>
</div>

    <!-- Go Back to Homepage -->
    <div class="tile home" onclick="location.href='index.php'" 
         style="background:#e0e0e0; width:200px; padding:15px; margin:auto; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.15); text-align:center;">
        <div class="emoji">🏠</div>
        <h3 style="margin:10px 0; font-size:18px;">Go back to Homepage</h3>
        <p style="font-size:14px; margin:0;">Return to the main site</p>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Hotel Lilies. All Rights Reserved.</p>
</footer>

</body>
</html>
