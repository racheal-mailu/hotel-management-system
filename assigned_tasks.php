<?php
session_start();
include('db_connect.php');

// ✅ Staff session check (only check staff_id)
if(!isset($_SESSION['staff_id'])){
    header("Location: staff_login.php");
    exit;
}

$staff_id = $_SESSION['staff_id'];

// ✅ Handle status update
if(isset($_POST['update_status'])){
    $task_id = intval($_POST['task_id']);
    $status = $_POST['status'];

    $updateQuery = "UPDATE assigned_tasks SET status=? WHERE task_id=? AND staff_id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sii", $status, $task_id, $staff_id);
    $stmt->execute();
}

// ✅ Fetch all tasks for this staff member
$query = "SELECT * FROM assigned_tasks WHERE staff_id=? ORDER BY deadline ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff - Assigned Tasks</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        h2 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #009688; color: white; }
        select { padding: 5px; border-radius: 5px; }
        input[type="submit"] { padding: 5px 10px; background: #2575fc; color: white; border: none; border-radius: 5px; cursor: pointer; }
        input[type="submit"]:hover { background: #0069c0; }
        a.back { display: inline-block; margin-top: 15px; padding: 8px 15px; background: #009688; color: white; text-decoration: none; border-radius: 5px; }
        p.no-tasks { text-align: center; font-size: 16px; color: #666; }
    </style>
</head>
<body>

<h2>Assigned Tasks</h2>

<?php if($result->num_rows > 0): ?>
<table>
    <tr>
        <th>Task ID</th>
        <th>Description</th>
        <th>Status</th>
        <th>Deadline</th>
        <th>Action</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['task_id']; ?></td>
        <td><?php echo htmlspecialchars($row['task_description']); ?></td>
        <td>
            <form method="POST" style="margin:0;">
                <input type="hidden" name="task_id" value="<?php echo $row['task_id']; ?>">
                <select name="status">
                    <option value="Pending" <?php if($row['status']=='Pending') echo 'selected'; ?>>Pending</option>
                    <option value="In Progress" <?php if($row['status']=='In Progress') echo 'selected'; ?>>In Progress</option>
                    <option value="Completed" <?php if($row['status']=='Completed') echo 'selected'; ?>>Completed</option>
                </select>
        </td>
        <td><?php echo $row['deadline']; ?></td>
        <td>
                <input type="submit" name="update_status" value="Update">
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p class="no-tasks">No tasks assigned.</p>
<?php endif; ?>

<div style="text-align:center;">
    <a class="back" href="staff_portal.php">Back to Staff Portal</a>
</div>

</body>
</html>
