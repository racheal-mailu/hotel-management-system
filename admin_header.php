<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if none exists
}

// Redirect to login if admin is not logged in
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: login.php");
    exit();
}

// Optional: set admin_name fallback to avoid warnings
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>


<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f4; }
        nav {
            background: #007bff;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav .logo { font-size: 20px; font-weight: bold; }
        nav ul { list-style: none; margin: 0; padding: 0; display: flex; align-items: center; }
        nav ul li { margin-left: 20px; }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background 0.2s;
        }
        nav ul li a:hover { background: #0056b3; }
        .admin-info { font-weight: bold; margin-left: 20px; color: #fff; }
    </style>
</head>
<body>
<nav>
    <div class="logo">Hotel Lilies Admin</div>
    <ul>
        <li><a href="admin_dashboard.php">Home</a></li>
        <li><a href="add_bookings.php">Add Booking</a></li>
        <li><a href="view_bookings.php">View Bookings</a></li>
        <li><a href="customers_rooms.php">Manage Rooms</a></li>
        <li><a href="staff.php">Manage Staff</a></li>
        <li><a href="payments.php">Payments</a></li>
        <li class="admin-info">Hello, <?php echo htmlspecialchars($admin_name); ?></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
</body>
</html>
