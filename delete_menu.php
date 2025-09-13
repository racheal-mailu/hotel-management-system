<?php
include('db_connect.php');

$menu_id = intval($_GET['id']);

// Fetch menu item before delete
$query = "SELECT * FROM menu_items WHERE menu_id = $menu_id LIMIT 1";
$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    die("Item not found!");
}

// Delete item
$deleteQuery = "DELETE FROM menu_items WHERE menu_id = $menu_id";
if (mysqli_query($conn, $deleteQuery)) {
    echo "<script>alert('Menu item deleted successfully!'); window.location='admin_menu.php';</script>";
    exit;
} else {
    echo "Error deleting: " . mysqli_error($conn);
}
?>
