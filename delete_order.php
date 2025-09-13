<?php
include('db_connect.php');

if (!isset($_GET['id'])) {
    die("Order ID not provided.");
}

$order_id = intval($_GET['id']);

// Delete related order items first to avoid foreign key issues
$deleteItemsQuery = "DELETE FROM order_items WHERE order_id=?";
$stmt = $conn->prepare($deleteItemsQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();

// Delete the order
$deleteOrderQuery = "DELETE FROM orders WHERE order_id=?";
$stmt2 = $conn->prepare($deleteOrderQuery);
$stmt2->bind_param("i", $order_id);

if ($stmt2->execute()) {
    echo "<script>alert('Order deleted successfully.'); window.location.href='reports.php';</script>";
} else {
    echo "Error deleting order: " . $conn->error;
}
