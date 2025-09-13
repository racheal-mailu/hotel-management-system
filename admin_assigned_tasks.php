<?php
session_start();
include('db_connect.php');

// Admin session check
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit;
}

// Handle new task assignment
if(isset($_POST['add_task'])){
    $staff_id = intval($_POST['staff_id']);
    $task_description = $_POST['task_description'];
    $deadline = $_POST['deadline'];

    $insertQuery = "INSERT INTO assigned_tasks (staff_id, task_description, status, deadline) 
                    VALUES ($staff_id, '$task_description', 'Pending', '$deadline')";
    mysqli_query($conn, $insertQuery) or die("Task assignment failed: " . mysqli_error($conn));
    header("Location: admin_assigned_tasks.php");
    exit;
}

// Handle status update from inline form
if(isset($_POST['update_status'])){
    $task_id = intval($_POST['task_id']);
    $status = $_POST['status'];
    $updateQuery = "UPDATE assigned_tasks SET status='$status' WHERE task_id=$task_id";
    mysqli_query($conn, $updateQuery) or die("Task update failed: " . mysqli_error($conn));
    header("Location: admin_assigned_tasks.php");
    exit;
}

// Fetch all tasks with staff name
$query = "SELECT t.task_id, s.fullname, t.task_description, t.status, t.deadline
          FROM assigned_tasks t
          LEFT JOIN staff s ON t.staff_id = s.staff_id
          ORDER BY t.task_id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Assigned Tasks</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background:#f4f4f4; }
        table { width:100%; border-collapse: collapse; margin-bottom:20px; background:#fff; }
        th, td { padding:10px; border:1px solid #ccc; text-align:center; }
        th { background:#009688; color:white; }
        select, input[type="text"], input[type="date"] { padding:5px; width:90%; }
        input[type="submit"] { padding:5px 10px; background:#2575fc; color:white; border:none; border-radius:5px; cursor:pointer; }
        form.inline { display:inline; }
        a.back { padding:8px 15px; background:#009688; color:white; text-decoration:none; border-radius:5px; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Assigned Tasks</h2>

<!-- Assign New Task -->
<h3>Add New Task</h3>
<form method="POST" style="margin-bottom:30px;">
    <label>Staff:</label>
    <select name="staff_id" required>
        <?php
        $staffQuery = mysqli_query($conn, "SELECT staff_id, fullname FROM staff");
        while($staff = mysqli_fetch_assoc($staffQuery)){
            echo "<option value='{$staff['staff_id']}'>{$staff['fullname']}</option>";
        }
        ?>
    </select>
    <label>Task Description:</label>
    <input type="text" name="task_description" required>
    <label>Deadline:</label>
    <input type="date" name="deadline" required>
    <input type="submit" name="add_task" value="Assign Task">
</form>

<!-- Existing Tasks Table -->
<table>
    <tr>
        <th>Task ID</th>
        <th>Staff Name</th>
        <th>Task Description</th>
        <th>Status</th>
        <th>Deadline</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?php echo $row['task_id']; ?></td>
        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
        <td><?php echo htmlspecialchars($row['task_description']); ?></td>
        <td>
            <form method="POST" class="inline">
                <input type="hidden" name="task_id" value="<?php echo $row['task_id']; ?>">
                <select name="status">
                    <option value="Pending" <?php if($row['status']=='Pending') echo 'selected'; ?>>Pending</option>
                    <option value="In Progress" <?php if($row['status']=='In Progress') echo 'selected'; ?>>In Progress</option>
                    <option value="Completed" <?php if($row['status']=='Completed') echo 'selected'; ?>>Completed</option>
                </select>
                <input type="submit" name="update_status" value="Update">
            </form>
        </td>
        <td><?php echo $row['deadline']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<div style="text-align:center;">
    <a class="back" href="admin_dashboard.php">Back to Admin Dashboard</a>
</div>

</body>
</html>
