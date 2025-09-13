<?php
session_start();
include("db_connect.php"); 

$booking_id = $_SESSION['booking_id'] ?? null;

// Get latest order ID from query string (after checkout redirect)
$latest_order_id = isset($_GET['latest']) ? intval($_GET['latest']) : null;

// Fetch orders for this booking
$sql_orders = "SELECT * FROM orders WHERE booking_id ".($booking_id ? "=?" : "IS NULL")." ORDER BY order_date DESC";
$stmt_orders = $conn->prepare($sql_orders);
if ($booking_id) $stmt_orders->bind_param("i", $booking_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f4f7fb; }
.order-container { max-width: 900px; margin: auto; }
.order-box { background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); transition: background-color 1s ease; }
.order-box h3 { margin: 0 0 10px 0; display: inline-block; }
.status { font-weight: bold; color: green; margin-left: 10px; }
.view-btn { float: right; padding: 5px 10px; cursor: pointer; background: #007bff; color: #fff; border: none; border-radius: 5px; }
.view-btn:hover { background: #0069d9; }
.order-details { display: none; margin-top: 10px; }
table { width: 100%; border-collapse: collapse; margin-top: 5px; }
th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
th { background: #f4f4f4; }
.total { font-weight: bold; text-align: right; }
.back-btn { display: inline-block; margin-top: 20px; padding: 8px 15px; background-color: #6c757d; color: white; border-radius: 5px; text-decoration: none; }
.back-btn:hover { background-color: #5a6268; }

/* Highlight latest order */
.order-highlight {
    border: 2px solid #ffc107;
    background-color: #fff9e6;
    box-shadow: 0 0 10px rgba(255,193,7,0.6);
}
</style>
</head>
<body>
<div class="order-container">
<h1>My Orders</h1>

<?php
if ($result_orders->num_rows > 0) {
    while ($order = $result_orders->fetch_assoc()) {
        $order_id = $order['order_id'];
        $order_date = $order['order_date'];
        $total = $order['total'];
        $status = $order['status'];

        // Fetch items for this order
        $stmt_items = $conn->prepare("SELECT oi.*, m.name FROM order_items oi JOIN menu_items m ON oi.menu_id = m.menu_id WHERE order_id=?");
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        ?>

        <div class="order-box <?php echo ($latest_order_id == $order_id) ? 'order-highlight' : ''; ?>">
            <h3>Order ID: #<?php echo $order_id; ?> | Date: <?php echo $order_date; ?></h3>
            <span class="status">Status: <?php echo htmlspecialchars($status); ?></span>
            <button class="view-btn" onclick="toggleDetails(<?php echo $order_id; ?>)">View Details</button>

            <div class="order-details" id="details-<?php echo $order_id; ?>" 
                 style="<?php echo ($latest_order_id == $order_id) ? 'display:block;' : 'display:none;'; ?>">
                <table>
                    <tr><th>Item</th><th>Quantity</th><th>Price (KSh)</th><th>Subtotal (KSh)</th></tr>
                    <?php
                    while ($item = $result_items->fetch_assoc()) {
                        $subtotal = $item['price'] * $item['quantity'];
                        echo "<tr>
                            <td>".htmlspecialchars($item['name'])."</td>
                            <td>{$item['quantity']}</td>
                            <td>".number_format($item['price'],2)."</td>
                            <td>".number_format($subtotal,2)."</td>
                        </tr>";
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="total">Total:</td>
                        <td><strong><?php echo number_format($total,2); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>

    <?php
    }
} else {
    echo "<p>You have no orders yet. <a href='menu.php'>Place an order now!</a></p>";
}
?>

<a href="menu.php" class="back-btn">Back to Menu</a>
</div>

<script>
function toggleDetails(orderId) {
    const details = document.getElementById('details-' + orderId);
    if(details.style.display === "none" || details.style.display === "") {
        details.style.display = "block";
    } else {
        details.style.display = "none";
    }
}

// Optional: Fade out highlight after 5 seconds
window.onload = function() {
    const highlight = document.querySelector('.order-highlight');
    if (highlight) {
        setTimeout(() => {
            highlight.classList.remove('order-highlight');
        }, 5000);
    }
};
</script>

</body>
</html>
