<?php
session_start();
include('db_connect.php');

// Optional: redirect if not logged in as admin
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header("Location: admin_login.php");
//     exit;
// }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Hotel Lilies</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #f4f4f9; 
        }
        h1 { 
            text-align: center; 
            margin: 30px 0; 
        }
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card a {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            text-decoration: none;
            margin-bottom: 10px;
        }
        .card p {
            color: #555;
            font-size: 14px;
        }
        /* Different accent colors for cards */
        .add-booking { border-top: 5px solid #4CAF50; }
        .view-bookings { border-top: 5px solid #2196F3; }
        .manage-menus { border-top: 5px solid #FF5722; } /* New tile color */
        .manage-rooms { border-top: 5px solid #FF9800; }
        .manage-staff { border-top: 5px solid #9C27B0; }
        .assigned-tasks { border-top: 5px solid #3F51B5; }
        .payments { border-top: 5px solid #009688; }
        .reports { border-top: 5px solid #F44336; }
        /* Logout card */
        .logout-card {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            margin: 30px auto;
            max-width: 300px;
        }
        .logout-card a {
            display: inline-block;
            padding: 10px 20px;
            background: #333;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }
        .logout-card a:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <h1>Welcome, Administrator</h1>

    <div class="dashboard-container">
        <div class="card add-booking">
            <a href="add_booking.php">Add Booking</a>
            <p>Create new bookings for customers quickly.</p>
        </div>
        <div class="card view-bookings">
            <a href="view_bookings.php">View Bookings</a>
            <p>Check and manage all customer bookings.</p>
        </div>
        <!-- ✅ New Manage Menus tile -->
        <div class="card manage-menus">
            <a href="admin_menu.php">Manage Menus</a>
            <p>Add, update, or remove menu items.</p>
        </div>
        <div class="card manage-rooms">
            <a href="manage_rooms.php">Manage Rooms</a>
            <p>Update room availability and details.</p>
        </div>
        <div class="card manage-staff">
            <a href="staff.php">Manage Staff</a>
            <p>Add and manage hotel staff information.</p>
        </div>
        <div class="card assigned-tasks">
            <a href="admin_assigned_tasks.php">Assigned Tasks 📝</a>
            <p>Assign and manage staff tasks.</p>
        </div>
        <div class="card payments">
            <a href="admin_payments.php">Payments</a>
            <p>Track and process customer payments.</p>
        </div>
        <div class="card reports">
            <a href="reports.php">Reports</a>
            <p>View financial and booking reports.</p>
        </div>
    </div>

    <div class="logout-card">
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
