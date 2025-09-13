<?php
include('db_connect.php');

if(!isset($_GET['staff_id'])){
    die("Staff ID not provided.");
}

$staff_id = intval($_GET['staff_id']);

// Delete staff
$deleteQuery = "DELETE FROM staff WHERE staff_id='$staff_id'";
$result = mysqli_query($conn, $deleteQuery);

if($result){
    header("Location: staff.php?message=Staff+deleted+successfully");
    exit();
} else {
    die("Error deleting staff: " . mysqli_error($conn));
}
?>
