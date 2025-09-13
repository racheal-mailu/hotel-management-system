<?php
include('db_connect.php');

if(isset($_POST['add_staff'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $phone = $_POST['phone'];
    $hire_date = $_POST['hire_date'];

    // Strong default password for all staff
    $defaultPassword = "StaffStrong123!"; 
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

    // Check if email already exists
    $checkQuery = "SELECT * FROM staff WHERE email=?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        echo "Email already exists!";
    } else {
        $insertQuery = "INSERT INTO staff (fullname, email, password, role, phone, hire_date) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssssss", $fullname, $email, $hashedPassword, $role, $phone, $hire_date);
        if($stmt->execute()){
            echo "Staff added successfully! Default password: $defaultPassword";
        } else {
            echo "Error adding staff: ".$conn->error;
        }
    }
}
?>

<!-- Simple HTML Form -->
<form method="POST">
    <input type="text" name="fullname" placeholder="Full Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="role" placeholder="Role (Staff/Receptionist/etc.)" required><br>
    <input type="text" name="phone" placeholder="Phone"><br>
    <input type="date" name="hire_date" required><br>
    <button type="submit" name="add_staff">Add Staff</button>
</form>
