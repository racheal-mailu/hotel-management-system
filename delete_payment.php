<?php
include('db_connect.php');

if (isset($_GET['id'])) {
    $payment_id = intval($_GET['id']);

    // Delete query
    $delete = "DELETE FROM payments WHERE payment_id = $payment_id";
    if (mysqli_query($conn, $delete)) {
        echo "<script>alert('Payment deleted successfully!'); window.location='admin_payments.php';</script>";
    } else {
        echo "<script>alert('Error deleting payment: " . mysqli_error($conn) . "'); window.location='admin_payments.php';</script>";
    }
} else {
    header("Location: admin_payments.php");
    exit;
}
?>
