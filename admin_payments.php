<?php
session_start();
include('db_connect.php');

// Handle new payment form submission
if (isset($_POST['add_payment'])) {
    $booking_id = $_POST['booking_id'];
    $amount = $_POST['amount'];
    $method = $_POST['payment_method'];

    $insert = "INSERT INTO payments (booking_id, amount, payment_date, payment_method, status)
               VALUES ('$booking_id', '$amount', NOW(), '$method', 'Pending')";
    if (mysqli_query($conn, $insert)) {
        echo "<script>alert('Payment recorded successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch all bookings for dropdown (with price_per_night)
$bookings_q = "
SELECT b.booking_id, b.fullname, b.email, r.room_number, r.price_per_night, b.check_in, b.check_out
FROM bookings b
JOIN rooms r ON b.room_id = r.room_id
ORDER BY b.booking_id DESC
";
$bookings_res = mysqli_query($conn, $bookings_q);

// Fetch all payments with booking info
$query = "
SELECT p.payment_id, p.amount, p.payment_date, p.payment_method, p.status,
       b.booking_id, b.room_id, b.check_in, b.check_out,
       b.fullname AS customer_name, b.email AS customer_email,
       r.room_number, r.price_per_night
FROM payments p
JOIN bookings b ON p.booking_id = b.booking_id
JOIN rooms r ON b.room_id = r.room_id
ORDER BY p.payment_date DESC
";
$result = mysqli_query($conn, $query) or die("Query Failed: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Payments</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ccc;
            background: #f9f9f9;
            border-radius: 8px;
            width: 500px;
        }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button {
            margin-top: 15px;
            padding: 10px 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover { background: #218838; }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: left;
        }
        th { background-color: #f4f4f4; }
        tr:hover { background-color: #f9f9f9; }

        .status-pending { color: red; font-weight: bold; }
        .status-paid { color: green; font-weight: bold; }
    </style>
</head>
<body>

<h2>Record New Payment</h2>
<form method="post" action="">
    <label for="booking_id">Select Booking:</label>
    <select name="booking_id" id="booking_id" required onchange="setAmount()">
        <option value="">-- Select Booking --</option>
        <?php while ($b = mysqli_fetch_assoc($bookings_res)) { ?>
            <option value="<?php echo $b['booking_id']; ?>"
                data-price="<?php echo $b['price_per_night']; ?>"
                data-checkin="<?php echo $b['check_in']; ?>"
                data-checkout="<?php echo $b['check_out']; ?>"> 
                Booking #<?php echo $b['booking_id']; ?> - 
                <?php echo $b['fullname']; ?> 
                (Room <?php echo $b['room_number']; ?>, 
                <?php echo $b['check_in']; ?> → <?php echo $b['check_out']; ?>)
            </option>
        <?php } ?>
    </select>

    <label for="amount">Amount:</label>
    <input type="number" step="0.01" name="amount" id="amount" readonly required>

    <label for="payment_method">Payment Method:</label>
    <select name="payment_method" id="payment_method" required>
        <option value="Cash">Cash</option>
        <option value="Mpesa">Mpesa</option>
        <option value="Card">Card</option>
        <option value="Bank Transfer">Bank Transfer</option>
    </select>

    <button type="submit" name="add_payment">Save Payment</button>
</form>

<script>
function setAmount() {
    const select = document.getElementById("booking_id");
    const amountInput = document.getElementById("amount");
    const selectedOption = select.options[select.selectedIndex];

    const price = parseFloat(selectedOption.getAttribute("data-price")) || 0;
    const checkin = new Date(selectedOption.getAttribute("data-checkin"));
    const checkout = new Date(selectedOption.getAttribute("data-checkout"));

    let nights = 1;
    if (!isNaN(checkin) && !isNaN(checkout)) {
        const diffTime = checkout.getTime() - checkin.getTime();
        nights = diffTime / (1000 * 3600 * 24); // convert ms to days
        if (nights < 1) nights = 1;
    }

    const total = price * nights;
    amountInput.value = total.toFixed(2);
}
</script>

<h2>Payments List</h2>

<table>
    <tr>
        <th>Payment ID</th>
        <th>Booking ID</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Room</th>
        <th>Check-in</th>
        <th>Check-out</th>
        <th>Amount</th>
        <th>Method</th>
        <th>Status</th>
        <th>Payment Date</th>
        <th>Actions</th>
    </tr>

<?php if(mysqli_num_rows($result) > 0): ?>
   <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?php echo $row['payment_id']; ?></td>
        <td><?php echo $row['booking_id']; ?></td>
        <td><?php echo $row['customer_name']; ?></td>
        <td><?php echo $row['customer_email']; ?></td>
        <td><?php echo $row['room_number']; ?></td>
        <td><?php echo $row['check_in']; ?></td>
        <td><?php echo $row['check_out']; ?></td>
        <td><?php echo number_format($row['amount'], 2); ?></td>
        <td><?php echo $row['payment_method']; ?></td>
        <td class="status-<?php echo strtolower($row['status']); ?>">
            <?php echo ucfirst($row['status']); ?>
        </td>
        <td><?php echo $row['payment_date']; ?></td>
        <td>
            <a href="edit_payment.php?id=<?php echo $row['payment_id']; ?>" 
               style="padding:5px 10px; background:#007bff; color:#fff; text-decoration:none; border-radius:4px;">Edit</a>
            <a href="delete_payment.php?id=<?php echo $row['payment_id']; ?>" 
               onclick="return confirm('Are you sure you want to delete this payment?');"
               style="padding:5px 10px; background:#dc3545; color:#fff; text-decoration:none; border-radius:4px;">Delete</a>
        </td>
    </tr>
<?php endwhile; ?>

<?php else: ?>
    <tr>
        <td colspan="12">No payments found.</td>
    </tr>
<?php endif; ?>

</table>

<div style="margin-top: 20px; text-align: center;">
    <a href="admin_dashboard.php" 
       style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">
        Go Back to Admin Dashboard
    </a>
</div>
</body>
</html>
