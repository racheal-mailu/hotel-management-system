<?php
// customers_login.php
session_start();
include("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM customers WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['customer_id'] = $row['id'];
            $_SESSION['customer_name'] = $row['fullname'];
            header("Location: customers_rooms.php");
            exit;
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Login</title>
</head>
<body>
    <h2>Customer Login</h2>
    <form method="POST" action="">
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p>Don’t have an account? <a href="customers_register.php">Register here</a></p>
</body>
</html>
