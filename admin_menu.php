<?php
include('db_connect.php');

// Fetch all menu items
$query = "SELECT * FROM menu_items";
$result = mysqli_query($conn, $query);

// Function to get images for a menu item
function getMenuImages($itemName) {
    $baseFolder = __DIR__ . "/uploads/foods/" . $itemName . "/";
    $images = [];

    if (is_dir($baseFolder)) {
        // Grab all common image types
        $files = glob($baseFolder . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
        foreach ($files as $file) {
            $images[] = 'uploads/foods/' . rawurlencode($itemName) . '/' . rawurlencode(basename($file));
        }
    }

    if (empty($images)) {
        $images[] = 'uploads/default.jpg'; // fallback image
    }

    return $images;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .add-btn {
            display: inline-block;
            background: #4CAF50;
            color: #fff;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s ease;
        }
        .add-btn:hover {
            background: #388E3C;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 20px auto;
        }
        .menu-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.2s ease;
        }
        .menu-card:hover {
            transform: translateY(-5px);
        }
        .menu-title {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
            color: #333;
        }
        .menu-price {
            font-size: 16px;
            color: #009688;
            margin-bottom: 10px;
        }
        .menu-images {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }
        .menu-images img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .menu-actions {
            margin-top: 10px;
            text-align: center;
        }
        .menu-actions a {
            display: inline-block;
            padding: 8px 14px;
            margin: 5px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            transition: 0.2s ease;
        }
        .edit-btn {
            background: #2196F3;
            color: #fff;
        }
        .edit-btn:hover {
            background: #1976D2;
        }
        .delete-btn {
            background: #F44336;
            color: #fff;
        }
        .delete-btn:hover {
            background: #C62828;
        }
    </style>
</head>
<body>
    <h1>Menu Items</h1>

    <p style="text-align:center; margin-bottom:20px;">
        <a href="add_menu.php" class="add-btn">➕ Add New Item</a>
    </p>

    <div class="menu-grid">
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <div class="menu-card">
                <div class="menu-title"><?php echo htmlspecialchars($row['name']); ?></div>
                <div class="menu-price">Ksh <?php echo number_format($row['price']); ?></div>
                <div class="menu-images">
                    <?php
                    $images = getMenuImages($row['name']);
                    foreach ($images as $img) {
                        echo '<img src="' . $img . '" alt="' . htmlspecialchars($row['name']) . '">';
                    }
                    ?>
                </div>
               <div class="menu-actions">
    <a href="edit_menu.php?id=<?php echo $row['menu_id']; ?>" class="edit-btn">✏️ Edit</a>
    <a href="delete_menu.php?id=<?php echo $row['menu_id']; ?>" class="delete-btn"
       onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($row['name']); ?>?');">
        🗑️ Delete
    </a>
</div>


            </div>
        <?php endwhile; ?>
    </div>
    <div style="margin-top:15px; text-align:center;">
    <a href="admin_dashboard.php" 
       style="display:inline-block; padding:10px 16px; background:#9E9E9E; color:white; text-decoration:none; border-radius:6px; font-weight:bold;">
        ⬅ Go Back to Admin Dashboard
    </a>
</div>

</body>
</html>
