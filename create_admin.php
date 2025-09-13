<?php
include('db_connect.php');

// Create first admin user
$username = "admin";
$password = "hotel@2025"; 

// Hash the password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Insert into admin_users table
$sql = "INSERT INTO admin_users (username, password) VALUES ('$username', '$hashed')";

if ($conn->query($sql) === TRUE) {
    echo "Admin user created successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
