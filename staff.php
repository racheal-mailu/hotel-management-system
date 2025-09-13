<?php
include('db_connect.php');

$message = "";

// Handle form submission (Add Staff)
if(isset($_POST['add_staff'])){
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $created_at = date('Y-m-d H:i:s');

    $insertQuery = "INSERT INTO staff (fullname, email, role, password, created_at) 
                    VALUES ('$fullname', '$email', '$role', '$password', '$created_at')";
    $result = mysqli_query($conn, $insertQuery);

    if($result){
        $message = "Staff member added successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

// Fetch all staff
$staffQuery = "SELECT * FROM staff ORDER BY staff_id ASC";
$staffResult = mysqli_query($conn, $staffQuery);
if (!$staffResult) die("Query failed: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Staff - Hotel Lilies Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .nav a { margin-right: 10px; text-decoration: none; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
        .action a { margin-right: 5px; }
        form { margin-top: 20px; margin-bottom: 20px; max-width: 500px; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; }
        .message { margin-top: 10px; color: green; }
    </style>
</head>
<body>
   
    <h1>Manage Staff</h1>

    <?php if($message) echo "<div class='message'>$message</div>"; ?>

    <!-- Add Staff Form -->
    <form method="POST" action="">
        <h2>Add New Staff</h2>
        <label for="fullname">Full Name:</label>
        <input type="text" name="fullname" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

      <label for="role">Role:</label>
<select name="role" required>
    <?php
    // Hard-coded roles
    $roles = ['Receptionist', 'Cleaner', 'Manager', 'Chef', 'Admin', 'Staff'];

    // Clean up current staff role: trim spaces, remove newlines
    $roleValue = isset($staff['role']) ? trim($staff['role']) : 'Staff';

    // If roleValue is not valid, default to 'Staff'
    if (!in_array($roleValue, $roles)) {
        $roleValue = 'Staff';
    }

    // Generate dropdown options
    foreach ($roles as $r) {
        $selected = ($roleValue === $r) ? 'selected' : '';
        echo "<option value='".htmlspecialchars($r)."' $selected>".htmlspecialchars($r)."</option>";
    }
    ?>
</select>


        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <button type="submit" name="add_staff">Add Staff</button>
    </form>

    <!-- Staff Table -->
    <table>
        <tr>
            <th>Staff ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($staffResult)) : ?>
        <tr>
            <td><?php echo $row['staff_id']; ?></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['role']); ?></td>
            <td class="action">
                <a href="edit_staff.php?staff_id=<?php echo $row['staff_id']; ?>">Edit</a>
                <a href="delete_staff.php?staff_id=<?php echo $row['staff_id']; ?>" onclick="return confirm('Are you sure you want to delete this staff?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <div style="margin-top: 20px; text-align: center;">
    <a href="admin_dashboard.php" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
        Go back to Admin Dashboard
    </a>
</div>

</body>
</html>
