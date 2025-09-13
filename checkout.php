<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("<p style='color:red; text-align:center;'>Your cart is empty. <a href='menu.php'>Go back to menu</a></p>");
}

// Get booking_id from session
$booking_id = $_SESSION['booking_id'] ?? null;

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Insert order
$stmt_order = $conn->prepare("INSERT INTO orders (booking_id, total, status, order_date) VALUES (?, ?, 'Pending', NOW())");
$stmt_order->bind_param("id", $booking_id, $total);
if ($stmt_order->execute()) {
    $order_id = $stmt_order->insert_id;

    // Insert all items into order_items
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $item) {
        $menu_id = $item['menu_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $stmt_item->bind_param("iiid", $order_id, $menu_id, $quantity, $price);
        $stmt_item->execute();
    }

    $stmt_item->close();
    $stmt_order->close();

    // Clear cart
    $_SESSION['cart'] = [];

    // Redirect to my_orders.php with latest order highlighted
    header("Location: my_orders.php?latest=$order_id");
    exit;

} else {
    die("Error creating order: " . $stmt_order->error);
}
?>
