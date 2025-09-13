<?php
// customers_register.php
include("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email exists
    $check = $conn->query("SELECT * FROM customers WHERE email='$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Email already registered. Please login.'); window.location='customers_login.php';</script>";
    } else {
        $sql = "INSERT INTO customers (fullname, email, password) VALUES ('$fullname', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful! You can login now.'); window.location='customers_login.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Registration</title>
</head>
<body>
    <h2>Register as Customer</h2>
    <form method="POST" action="">
        Full Name: <input type="text" name="fullname" required><br><br>
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="customers_login.php">Login here</a></p>
</body>
</html>
