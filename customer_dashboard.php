<?php
include('db_connect.php');

$email = $_SESSION['customer_email'] ?? '';
?>

<div class="dashboard-tile" style="background:#f9f9f9; border-radius:15px; padding:20px; max-width:500px; margin:auto; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
    <h2 style="text-align:center; color:#1976d2;">Customer Dashboard</h2>

    <?php if (!$email): ?>
        <p style="text-align:center; color:red;">No email found. Please make a booking first.</p>
        <a href="customer_portal.php" style="display:block; text-align:center; margin-top:15px; color:#fff; background:#007bff; padding:8px 15px; border-radius:5px; text-decoration:none;">Back to Customer Portal</a>
    <?php else: ?>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>

        <!-- Bookings & Payments -->
        <div class="section">
            <div class="section-header" onclick="toggleSection('bookings')">
                Bookings & Payments ▼
            </div>
            <div class="section-content" id="bookings">
                <?php
                $stmt = $conn->prepare("SELECT * FROM bookings WHERE email=? ORDER BY created_at DESC");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $bookings = $stmt->get_result();
                if($bookings->num_rows > 0):
                    while($b = $bookings->fetch_assoc()):
                ?>
                    <div class="booking">
                        <strong>Booking #<?php echo $b['booking_id']; ?></strong> | 
                        Check-in: <?php echo $b['check_in']; ?> | 
                        Check-out: <?php echo $b['check_out']; ?> | 
                        Payment: <?php 
                            $stmt2 = $conn->prepare("SELECT status, amount, payment_date FROM payments WHERE booking_id=? ORDER BY payment_date DESC LIMIT 1");
                            $stmt2->bind_param("i", $b['booking_id']);
                            $stmt2->execute();
                            $payment = $stmt2->get_result()->fetch_assoc();
                            echo $payment['status'] ?? 'Pending';
                        ?>
                        <br>
                        Amount: KSh <?php echo number_format($payment['amount'] ?? 0, 2); ?> |
                        Payment Date: <?php echo $payment['payment_date'] ?? '-'; ?>
                    </div>
                <?php
                    endwhile;
                else:
                    echo "<p>No bookings found.</p>";
                endif;
                ?>
            </div>
        </div>

        <!-- Orders -->
        <div class="section">
            <div class="section-header" onclick="toggleSection('orders')">
                Orders ▼
            </div>
            <div class="section-content" id="orders">
                <?php
                $stmt = $conn->prepare("SELECT * FROM bookings WHERE email=? ORDER BY created_at DESC");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $bookings = $stmt->get_result();
                $has_orders = false;
                while($b = $bookings->fetch_assoc()):
                    $stmt2 = $conn->prepare("SELECT o.*, oi.menu_id, oi.quantity, oi.price, m.name 
                                             FROM orders o 
                                             JOIN order_items oi ON o.order_id=oi.order_id 
                                             JOIN menu_items m ON oi.menu_id=m.menu_id
                                             WHERE o.booking_id=? ORDER BY o.order_date DESC");
                    $stmt2->bind_param("i", $b['booking_id']);
                    $stmt2->execute();
                    $orders = $stmt2->get_result();
                    if($orders->num_rows > 0):
                        $has_orders = true;
                        $current_order_id = null;
                        while($o = $orders->fetch_assoc()):
                            if($current_order_id != $o['order_id']):
                                if($current_order_id !== null) echo "</div>"; // close previous table
                                echo "<div class='order'>";
                                echo "<strong>Order #{$o['order_id']} | Date: {$o['order_date']} | Status: {$o['status']}</strong>";
                                echo "<table style='width:100%; margin-top:5px; border-collapse:collapse;'>";
                                echo "<tr><th>Item</th><th>Qty</th><th>Price (KSh)</th><th>Subtotal</th></tr>";
                                $current_order_id = $o['order_id'];
                            endif;
                            $subtotal = $o['price'] * $o['quantity'];
                            echo "<tr>
                                    <td>{$o['name']}</td>
                                    <td>{$o['quantity']}</td>
                                    <td>".number_format($o['price'],2)."</td>
                                    <td>".number_format($subtotal,2)."</td>
                                  </tr>";
                        endwhile;
                        echo "<tr><td colspan='3' style='text-align:right; font-weight:bold;'>Total:</td>
                              <td>".number_format($b['total'] ?? 0,2)."</td></tr>";
                        echo "</table></div>";
                    endif;
                endwhile;
                if(!$has_orders) echo "<p>No orders found.</p>";
                ?>
            </div>
        </div>

        <a href="customer_portal.php" style="display:block; text-align:center; margin-top:15px; color:#fff; background:#007bff; padding:8px 15px; border-radius:5px; text-decoration:none;">Back to Customer Portal</a>
    <?php endif; ?>
</div>

<style>
.section-header { 
    padding: 10px 15px; 
    cursor: pointer; 
    background-color: #1976d2; 
    color: #fff; 
    font-weight: bold; 
    border-radius: 8px; 
    margin-top: 10px;
}
.section-content { 
    display: none; 
    padding: 10px 15px; 
    background-color: #e3f2fd; 
    color: #000; 
    border-radius: 0 0 8px 8px; 
    margin-bottom: 10px;
}
.booking { 
    background: #bbdefb; 
    padding: 10px; 
    margin-bottom: 10px; 
    border-radius: 8px; 
    border-left: 5px solid #1565c0; 
}
.order { 
    background: #ffe0b2; 
    padding: 10px; 
    margin-bottom: 10px; 
    border-radius: 8px; 
    border-left: 5px solid #fb8c00; 
}
table, th, td { border: 1px solid #ccc; border-collapse: collapse; padding:5px; text-align:left; }
th { background:#f0f0f0; }
</style>

<script>
function toggleSection(id){
    const content = document.getElementById(id);
    if(content.style.display === "none" || content.style.display === "") content.style.display = "block";
    else content.style.display = "none";
}
</script>
