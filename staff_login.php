<?php
session_start();
include('db_connect.php');

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch staff by email
    $query = "SELECT * FROM staff WHERE email=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $staff = $result->fetch_assoc();
        if(password_verify($password, $staff['password'])){
            $_SESSION['staff_logged_in'] = true;
            $_SESSION['staff_id'] = $staff['staff_id'];
            $_SESSION['role'] = $staff['role'];
            header("Location: staff_portal.php");
            exit;
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Login - Hotel Lilies</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .login-box {
        background: #fff;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        width: 320px;
        text-align: center;
    }
    .login-box h2 {
        margin-bottom: 20px;
        color: #333;
    }
    .login-box input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    .login-box button {
        width: 100%;
        padding: 10px;
        background: #2575fc;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    .login-box button:hover {
        background: #6a11cb;
    }
    .error {
        color: red;
        margin-bottom: 10px;
    }
</style>
</head>
<body>

<div class="login-box">
    <h2>Staff Login</h2>
    <?php if(isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <p style="margin-top:10px; font-size:0.9rem; color:#555;">Default password for new staff: <strong>StaffStrong123!</strong></p>
</div>

</body>
</html>
