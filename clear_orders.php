<?php
include('db_connect.php');

if (isset($_POST['clear_orders'])) {
    try {
        // Start transaction
        mysqli_begin_transaction($conn);

        // 1️⃣ Delete all order items first
        $deleteItems = "DELETE FROM order_items";
        if (!mysqli_query($conn, $deleteItems)) {
            throw new Exception("Error clearing order items: " . mysqli_error($conn));
        }

        // 2️⃣ Delete all orders
        $deleteOrders = "DELETE FROM orders";
        if (!mysqli_query($conn, $deleteOrders)) {
            throw new Exception("Error clearing orders: " . mysqli_error($conn));
        }

        // Commit transaction
        mysqli_commit($conn);
        $message = "✅ All orders cleared successfully.";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "❌ " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clear Orders</title>
    <style>
        .btn-clear {
            padding: 10px 15px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn-clear:hover { background-color: #d32f2f; }
        .message { margin: 15px 0; font-weight: bold; }
    </style>
    <script>
        function confirmClear() {
            return confirm('Are you sure you want to clear all orders? This cannot be undone.');
        }
    </script>
</head>
<body>
    <h1>Clear All Orders</h1>

    <?php if (isset($message)) : ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post" onsubmit="return confirmClear();">
        <button type="submit" name="clear_orders" class="btn-clear">Clear Orders</button>
    </form>
</body>
</html>
