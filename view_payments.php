<?php
include 'db_connect.php';
include 'header.php';

$sql = "SELECT * FROM payments"; // replace with your actual query
$result = $conn->query($sql);

if (!$result) {
    die("Query Failed: " . $conn->error);
}


$result = $conn->query($sql);
?>

<h2>Payments List</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>Payment ID</th>
        <th>Customer Name</th>
        <th>Room Number</th>
        <th>Amount</th>
        <th>Payment Method</th>
        <th>Payment Date</th>
    </tr>
    <?php if ($result->num_rows > 0) { ?>
        <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['payment_id'] ?></td>
                <td><?= $row['full_name'] ?></td>
                <td><?= $row['room_number'] ?></td>
                <td><?= number_format($row['amount'], 2) ?></td>
                <td><?= $row['payment_method'] ?></td>
                <td><?= $row['payment_date'] ?></td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr><td colspan="6">No payments found</td></tr>
    <?php } ?>
</table>

<?php include 'footer.php'; ?>
