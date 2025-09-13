<?php
include('db_connect.php');

if (!isset($_GET['id'])) {
    header("Location: admin_payments.php");
    exit;
}

$payment_id = intval($_GET['id']);

// Fetch payment details
$query = "
    SELECT p.payment_id, p.amount, p.payment_method, p.status,
           b.booking_id, b.fullname, r.room_number
    FROM payments p
    JOIN bookings b ON p.booking_id = b.booking_id
    JOIN rooms r ON b.room_id = r.room_id
    WHERE p.payment_id = $payment_id
";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('Payment not found!'); window.location='admin_payments.php';</script>";
    exit;
}

$payment = mysqli_fetch_assoc($result);

// Handle update form submission
if (isset($_POST['update_payment'])) {
    $amount = $_POST['amount'];
    $method = $_POST['payment_method'];
    $status = $_POST['status'];

    $update = "UPDATE payments 
               SET amount='$amount', payment_method='$method', status='$status'
               WHERE payment_id=$payment_id";
    if (mysqli_query($conn, $update)) {
        echo "<script>alert('Payment updated successfully!'); window.location='admin_payments.php';</script>";
    } else {
        echo "<script>alert('Error updating payment: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Payment</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form {
            width: 400px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #f9f9f9;
        }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button {
            margin-top: 15px;
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

<h2>Edit Payment</h2>

<form method="post" action="">
    <p><strong>Booking:</strong> #<?php echo $payment['booking_id']; ?> - 
       <?php echo $payment['fullname']; ?> (Room <?php echo $payment['room_number']; ?>)</p>

    <label for="amount">Amount:</label>
    <input type="number" step="0.01" name="amount" id="amount" value="<?php echo $payment['amount']; ?>" required>

    <label for="payment_method">Payment Method:</label>
    <select name="payment_method" id="payment_method" required>
        <option value="Cash" <?php if($payment['payment_method']=='Cash') echo 'selected'; ?>>Cash</option>
        <option value="Mpesa" <?php if($payment['payment_method']=='Mpesa') echo 'selected'; ?>>Mpesa</option>
        <option value="Card" <?php if($payment['payment_method']=='Card') echo 'selected'; ?>>Card</option>
        <option value="Bank Transfer" <?php if($payment['payment_method']=='Bank Transfer') echo 'selected'; ?>>Bank Transfer</option>
    </select>

    <label for="status">Payment Status:</label>
    <select name="status" id="status" required>
        <option value="Pending" <?php if($payment['status']=='Pending') echo 'selected'; ?>>Pending</option>
        <option value="Paid" <?php if($payment['status']=='Paid') echo 'selected'; ?>>Paid</option>
        <option value="Refunded" <?php if($payment['status']=='Refunded') echo 'selected'; ?>>Refunded</option>
    </select>

    <button type="submit" name="update_payment">Update Payment</button>
</form>

</body>
</html>
