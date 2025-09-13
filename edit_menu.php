<?php
include('db_connect.php');

$menu_id = intval($_GET['id']);

// Fetch existing menu item
$query = "SELECT * FROM menu_items WHERE menu_id = $menu_id LIMIT 1";
$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    die("Item not found!");
}

// Handle update form
if (isset($_POST['update_menu'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);

    $updateQuery = "UPDATE menu_items SET name='$name', price=$price WHERE menu_id=$menu_id";
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Menu item updated successfully!'); window.location='admin_menu.php';</script>";
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            padding: 20px;
        }
        .form-container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #2196F3;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        button:hover {
            background: #1976D2;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Menu Item</h2>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>

            <label>Price (Ksh):</label>
            <input type="number" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" step="0.01" required>

            <button type="submit" name="update_menu">💾 Save Changes</button>
        </form>
        <div style="margin-top:15px; text-align:center;">
    <a href="admin_menu.php" 
       style="display:inline-block; padding:10px 16px; background:#9E9E9E; color:white; text-decoration:none; border-radius:6px; font-weight:bold;">
        ⬅ Go Back to Admin Menu
    </a>
</div>

    </div>
</body>
</html>
