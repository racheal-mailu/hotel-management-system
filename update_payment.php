<?php
include 'db_connect.php';

// Check if payment_id is provided
if (!isset($_GET['payment_id']) || empty($_GET['payment_id'])) {
    die("Invalid request.");
}

$payment_id = intval($_GET['payment_id']);

// Fetch payment details
$sql = "SELECT * FROM payments WHERE payment_id = $payment_id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $payment = $result->fetch_assoc();
} else {
    die("Payment not found.");
}

// Update payment if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $status = $_POST['status'];

    $update_sql = "UPDATE payments 
                   SET amount = '$amount', payment_method = '$payment_method', status = '$status' 
                   WHERE payment_id = $payment_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Payment updated successfully.'); window.location.href='view_payments.php';</script>";
    } else {
        echo "Error updating payment: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
            max-width: 400px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 15px;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            background: #28a745;
            color: white;
            font-size: 14px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .nav {
            margin-bottom: 20px;
        }
        .nav a {
            text-decoration: none;
            padding: 8px 15px;
            background: #007BFF;
            color: white;
            border-radius: 4px;
            margin-right: 10px;
        }
        .nav a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="nav">
    <a href="index.php">Home</a>
    <a href="view_payments.php">View Payments</a>
</div>

<h2>Update Payment</h2>

<form method="POST">
    <label>Amount</label>
    <input type="number" step="0.01" name="amount" value="<?php echo htmlspecialchars($payment['amount']); ?>" required>

    <label>Payment Method</label>
    <select name="payment_method" required>
        <option value="Cash" <?php if ($payment['payment_method'] == 'Cash') echo 'selected'; ?>>Cash</option>
        <option value="Card" <?php if ($payment['payment_method'] == 'Card') echo 'selected'; ?>>Card</option>
        <option value="Mobile" <?php if ($payment['payment_method'] == 'Mobile') echo 'selected'; ?>>Mobile</option>
    </select>

    <label>Status</label>
    <select name="status" required>
        <option value="Paid" <?php if ($payment['status'] == 'Paid') echo 'selected'; ?>>Paid</option>
        <option value="Pending" <?php if ($payment['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
    </select>

    <button type="submit">Update Payment</button>
</form>

</body>
</html>
