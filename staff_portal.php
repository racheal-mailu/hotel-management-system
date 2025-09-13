<?php
session_start();
if(!isset($_SESSION['staff_logged_in'])){
    header("Location: staff_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Portal - Hotel Lilies</title>
<style>
    body { font-family: Arial, sans-serif; margin:0; padding:20px; background:#f4f4f4; }
    h1 { text-align:center; margin-bottom:30px; }
    .dashboard { display:grid; grid-template-columns: repeat(auto-fit,minmax(250px,1fr)); gap:20px; margin-bottom: 40px; }
    .tile { background:#fff; padding:20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align:center; cursor:pointer; transition:transform .2s; }
    .tile:hover { transform:scale(1.05); }
    .emoji { font-size:40px; margin-bottom:10px; }
    footer { text-align:center; margin-top:40px; color:#666; }

    /* Top-right button container */
    .top-buttons {
        position: absolute;
        top: 20px;
        right: 20px;
    }
    .top-buttons a {
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        margin-left: 10px;
        font-weight: bold;
    }
    .btn-home { background: #009688; }
    .btn-logout { background: #f44336; }
    .top-buttons a:hover { opacity: 0.85; }
</style>
</head>
<body>

<!-- Top-right buttons -->
<div class="top-buttons">
    <a href="index.php" class="btn-home">Go Back to Homepage</a>
    <a href="staff_logout.php" class="btn-logout">Logout</a>
</div>

<h1>Staff Portal</h1>

<div class="dashboard">
    <!-- Manage Bookings -->
    <div class="tile" onclick="location.href='staff_bookings.php'">
    <div class="emoji">📑</div>
    <h2>Update Bookings</h2>
    <p>View and update customer bookings</p>
</div>


    <!-- View Assigned Tasks -->
    <div class="tile" onclick="location.href='assigned_tasks.php'">
        <div class="emoji">📝</div>
        <h2>Assigned Tasks</h2>
        <p>Check your daily tasks and duties</p>
    </div>
</div>

<footer>
<p>&copy; <?php echo date("Y"); ?> Hotel Lilies. Staff Portal.</p>
</footer>

</body>
</html>
