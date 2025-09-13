<?php
session_start();
include 'db_connect.php';

// Fetch menu items
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Menu</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; margin: 0; padding: 20px; }
        h1 { text-align: center; margin-bottom: 20px; }
        .menu-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .menu-item { background: #fff; border-radius: 10px; padding: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; }
        .menu-item img { width: 100%; height: 180px; object-fit: cover; border-radius: 10px; }
        .menu-item h3 { margin: 10px 0; }
        .price { font-weight: bold; color: #333; margin: 10px 0; }
        .category { font-size: 0.9em; color: gray; margin-bottom: 10px; }
        button { background: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #218838; }
    </style>
</head>
<body>
    <h1>Restaurant Menu</h1>
    <div class="menu-container">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="menu-item">
                <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                <h3><?php echo $row['name']; ?></h3>
                <p><?php echo $row['description']; ?></p>
                <div class="price">Ksh <?php echo number_format($row['price'], 2); ?></div>
                <div class="category"><?php echo $row['category']; ?></div>

                <!-- Add to Cart Form -->
                <form method="POST" action="cart.php">
                    <input type="hidden" name="menu_id" value="<?php echo $row['menu_id']; ?>">
                    <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                    <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                    <input type="number" name="quantity" value="1" min="1" style="width:60px;">
                    <button type="submit" name="add_to_cart">Add to Cart</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
