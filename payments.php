<?php
session_start();
include('db_connect.php');

$email = '';

// Step 1: Check if email is submitted via POST form
if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $_SESSION['customer_email'] = $email; // store for session
}

// Step 2: If no POST, check session
elseif (isset($_SESSION['customer_email'])) {
    $email = trim($_SESSION['customer_email']);
}

// Step 3: If still no email, show input form
if (!$email):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter Email to View Payments</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; }
        input, button { padding: 10px; font-size: 1rem; margin: 5px; }
        .btn { background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>View Your Payments</h2>
    <p>Enter your email used for booking:</p>
    <form method="post" action="">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button class="btn" type="submit">View Payments</button>
    </form>
    <p><a href="customer_portal.php">Back to Customer Portal</a></p>
</body>
</html>
<?php
exit; // stop further execution
endif;

// Step 4: Handle Pay Now action
if (isset($_GET['pay_id'])) {
    $payment_id = intval($_GET['pay_id']);
    $update = $conn->query("UPDATE payments SET status='Paid' WHERE payment_id=$payment_id");
    $msg = $update ? "Payment successful!" : "Payment failed: " . $conn->error;
}

// Step 5: Fetch payments for this email
$sql = "
SELECT p.*
FROM payments p
JOIN bookings b ON p.booking_id = b.booking_id
WHERE b.source = 'customer' AND b.email = ?
ORDER BY p.payment_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payments</title>
<style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .btn-pay { background-color: #28a745; color: #fff; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
    .btn-back { background-color: #007bff; color: #fff; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
    .status-paid { color: green; font-weight: bold; }
    .status-pending { color: red; font-weight: bold; }
</style>
</head>
<body>

<h2>Payments for <?php echo htmlspecialchars($email); ?></h2>

<?php if (isset($msg)) echo "<p>$msg</p>"; ?>

<?php if ($result->num_rows > 0): ?>
<table>
<tr>
    <th>#</th>
    <th>Booking ID</th>
    <th>Amount</th>
    <th>Payment Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>
<?php $i=1; while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo $i++; ?></td>
    <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
    <td><?php echo number_format($row['amount'], 2); ?></td>
    <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
    <td class="status-<?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></td>
    <td>
        <?php if ($row['status'] === 'Pending'): ?>
            <a class="btn-pay" href="?pay_id=<?php echo $row['payment_id']; ?>">Pay Now</a>
        <?php else: ?>
            Paid
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>No payments found for this email.</p>
<?php endif; ?>

<br>
<a href="customer_portal.php" class="btn-back">Back to Customer Portal</a>

</body>
</html>
