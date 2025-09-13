<?php
include('db_connect.php');

if(!isset($_GET['staff_id'])){
    die("Staff ID not provided.");
}

$staff_id = intval($_GET['staff_id']);
$message = "";

// Fetch existing staff data
$staffQuery = "SELECT * FROM staff WHERE staff_id='$staff_id'";
$staffResult = mysqli_query($conn, $staffQuery);
if(!$staffResult) die("Query failed: " . mysqli_error($conn));

$staff = mysqli_fetch_assoc($staffResult);
if(!$staff) die("Staff not found.");

// Handle form submission
if(isset($_POST['update_staff'])){
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Update query
    $updateQuery = "UPDATE staff SET fullname='$fullname', email='$email', role='$role'";

    // If password provided, hash it and update
    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $updateQuery .= ", password='$password'";
    }

    $updateQuery .= " WHERE staff_id='$staff_id'";

    $result = mysqli_query($conn, $updateQuery);
    if($result){
        $message = "Staff updated successfully!";
        $staff['fullname'] = $fullname;
        $staff['email'] = $email;
        $staff['role'] = $role;
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

// Define all possible roles to match your ENUM in SQL
$roles = ['Receptionist', 'Cleaner', 'Manager', 'Chef', 'Admin', 'Staff'];

// Determine the currently selected role
$roleValue = isset($staff['role']) && !empty($staff['role']) ? $staff['role'] : 'Staff';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Staff - Hotel Lilies Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .nav a { margin-right: 10px; text-decoration: none; color: #333; }
        form { max-width: 500px; margin-top: 20px; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; }
        .message { margin-top: 10px; color: green; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="admin_dashboard.php">Home</a> |
        <a href="staff.php">Manage Staff</a> |
        <a href="logout.php">Logout</a>
    </div>

    <h1>Edit Staff</h1>

    <?php if($message) echo "<div class='message'>$message</div>"; ?>

    <form method="POST" action="">
        <label for="fullname">Full Name:</label>
        <input type="text" name="fullname" value="<?php echo htmlspecialchars($staff['fullname']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($staff['email']); ?>" required>

     <label for="role">Role:</label>

<?php
// Debug: show exactly what $staff['role'] contains
echo '<pre>Current role: [' . $staff['role'] . ']</pre>';
?>

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

        <label for="password">Password (leave blank to keep current):</label>
        <input type="password" name="password">

        <button type="submit" name="update_staff">Update Staff</button>
    </form>
    <div style="margin-top: 20px; text-align: center;">
    <a href="admin_dashboard.php" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">
        Go back to Admin Dashboard
    </a>
</div>

</body>
</html>
