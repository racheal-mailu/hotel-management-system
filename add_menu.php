<?php
include('db_connect.php');

// Helper: slugify folder names (replace spaces/symbols)
function slugify($text) {
    $text = trim($text);
    $text = preg_replace('/[^A-Za-z0-9\-]+/', '_', $text);
    $text = trim($text, '_');
    return $text === '' ? 'item' : $text;
}

$error = '';
if (isset($_POST['add_menu'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);

    // Insert into DB (prepared for safety)
    $stmt = $conn->prepare("INSERT INTO menu_items (name, description, category, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $name, $description, $category, $price);

    if ($stmt->execute()) {
        $menuId = $stmt->insert_id;

        // Create sanitized folder for images
        $folderName = slugify($name);
        $folder = __DIR__ . "/uploads/foods/" . $folderName;
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        // Multi-image upload
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $fileName) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9_\.\-]/', '_', $fileName);
                    $target = $folder . "/" . $safeName;
                    move_uploaded_file($_FILES['images']['tmp_name'][$key], $target);
                }
            }
        }

        header("Location: admin_menu.php");
        exit;
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add Menu Item</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f9; padding:30px; }
        .card { max-width:600px; margin:20px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
        h2 { margin-top:0; }
        label { display:block; margin-top:10px; font-weight:600; }
        input[type=text], input[type=number], textarea, select {
            width:100%; padding:10px; margin-top:6px;
            border:1px solid #ddd; border-radius:6px;
        }
        textarea { min-height:80px; resize:vertical; }
        input[type=file] { margin-top:8px; }
        .actions { margin-top:16px; text-align:center; }
        .btn { padding:10px 16px; border-radius:6px; text-decoration:none; color:#fff; background:#2196F3; border:none; cursor:pointer; font-weight:600; }
        .btn.cancel { background:#9E9E9E; margin-left:8px; }
        .error { color:#F44336; margin-bottom:10px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Add New Menu Item</h2>
        <?php if ($error) echo '<div class="error">'.htmlspecialchars($error).'</div>'; ?>
        <form method="post" enctype="multipart/form-data">
            <label>Name</label>
            <input type="text" name="name" required>

            <label>Description</label>
            <textarea name="description"></textarea>

            <label>Category</label>
            <select name="category" required>
                <option value="">-- Select Category --</option>
                <option value="Main">Main</option>
                <option value="Side">Side</option>
                <option value="Drink">Drink</option>
                <option value="Dessert">Dessert</option>
            </select>

            <label>Price (Ksh)</label>
            <input type="number" step="0.01" name="price" required>

            <label>Upload Images (you can select multiple)</label>
            <input type="file" name="images[]" accept="image/*" multiple>

            <div class="actions">
                <button type="submit" name="add_menu" class="btn">➕ Add Item</button>
                <a href="admin_menu.php" class="btn cancel">Cancel</a>
            </div>
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
