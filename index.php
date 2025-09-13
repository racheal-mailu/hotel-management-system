<?php
include('db_connect.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Lilies - Welcome</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            color: #333;
            text-align: center;
        }
        header {
            background: linear-gradient(90deg, #009688, #26a69a);
            color: white;
            padding: 20px;
        }
        h1 {
            margin: 0;
        }
        .portal-container {
    display: flex;
    justify-content: center; /* Center tiles horizontally */
    flex-wrap: wrap; /* Allow wrapping on smaller screens */
    margin: 100px 20px; /* Added side margin for smaller screens */
    gap: 50px; /* Space between tiles */
}

.portal {
    background: white;
    width: 250px;
    height: 250px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: transform 0.2s, background 0.2s;
    flex: 1 1 250px; /* Makes tiles flexible but not smaller than 250px */
    max-width: 250px;
}

        .portal:hover {
            transform: translateY(-10px);
            background: #e0f2f1;
        }
        .portal h2 {
            margin-bottom: 10px;
            color: #009688;
        }
        .portal p {
            margin: 0;
            font-size: 1rem;
            color: #555;
        }
        footer {
            background: #343a40;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome to Hotel Lilies</h1>
    <p>Experience luxury and comfort at the heart of your stay</p>
</header>

<div class="portal-container">
    <div class="portal" onclick="location.href='customer_portal.php'">
        <h2>Customer Portal</h2>
        <p>View rooms, menu, make payments & chat</p>
    </div>
    
    <div class="portal" onclick="location.href='staff_login.php'">
    <h2>Staff Portal</h2>
    <p>Login to manage bookings & tasks</p>
</div>

    <div class="portal" onclick="location.href='admin_login.php'">
        <h2>Admin Portal</h2>
        <p>Login to manage bookings, rooms, and menu</p>
    </div>
</div>


<footer>
    <p>&copy; <?php echo date("Y"); ?> Hotel Lilies. All Rights Reserved.</p>
</footer>

</body>
</html>
