<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: login.php");
    exit();
}
include('db_connect.php');
include('admin_header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers & Rooms - Hotel Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        h2 { text-align: center; margin-bottom: 20px; }
        .table-container { overflow-x: auto; width: 95%; margin: auto; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #333; color: #fff; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-edit {
            background: #28a745;
            color: white;
        }
        .btn-edit:hover {
            background: #218838;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background: #c82333;
        }
    </style>
</head>
<body>

<h2>Customers & Booked Rooms</h2>

<div class="table-container">
<table>
    <tr>
        <th>Customer Name</th>
        <th>Email</th>
        <th>Room Number</th>
        <th>Room Type</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php
    $sql = "SELECT b.booking_id, c.fullname, c.email, r.room_number, r.room_type, r.status
            FROM customers c
            JOIN bookings b ON c.id = b.customer_id
            JOIN rooms r ON b.room_id = r.room_id";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['fullname']."</td>
                    <td>".$row['email']."</td>
                    <td>".$row['room_number']."</td>
                    <td>".$row['room_type']."</td>
                    <td>".$row['status']."</td>
                    <td>
                        <a href='edit_booking.php?id=".$row['booking_id']."'><button class='btn btn-edit'>Edit</button></a>
                        <a href='delete_booking.php?id=".$row['booking_id']."' onclick=\"return confirm('Are you sure you want to delete this booking?');\"><button class='btn btn-delete'>Delete</button></a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No customer bookings found</td></tr>";
    }
    ?>
</table>
</div>

</body>
</html>
