<?php
include 'db_connect.php';
session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// ✅ Handle AJAX Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_place_order'])) {
    $menu_id = $_POST['menu_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = max(1, intval($_POST['quantity']));
    $selected_image = $_POST['selected_image'] ?? '';

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['menu_id'] == $menu_id && $item['selected_image'] == $selected_image) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = [
            'menu_id' => $menu_id,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'selected_image' => $selected_image
        ];
    }
}

// ✅ Handle AJAX Remove (decrease quantity by 1)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_remove_one'])) {
    $menu_id = $_POST['menu_id'];
    $selected_image = $_POST['selected_image'] ?? '';

    foreach ($_SESSION['cart'] as $index => &$item) {
        if ($item['menu_id'] == $menu_id && $item['selected_image'] == $selected_image) {
            $item['quantity'] -= 1;
            if ($item['quantity'] <= 0) {
                unset($_SESSION['cart'][$index]); // remove item if 0
            }
            break;
        }
    }
}

// ✅ Render cart HTML (for AJAX or page load)
function renderCart() {
    $total = 0;
    $cart_html = '<h2>Your Cart</h2>';
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $item_total = $item['price'] * $item['quantity'];
            $total += $item_total;
            $cart_html .= "<div class='cart-item'>
                {$item['name']} x {$item['quantity']} - KSh $item_total
                <form class='remove-form' method='post' style='display:inline;'>
                    <input type='hidden' name='menu_id' value='{$item['menu_id']}'>
                    <input type='hidden' name='selected_image' value='{$item['selected_image']}'>
                    <button type='submit' name='ajax_remove_one'>Remove 1</button>
                </form>
            </div>";
        }
        $cart_html .= "<div><strong>Total: KSh $total</strong></div>";
        $cart_html .= "<form id='checkout-form' action='checkout.php' method='POST'>
                          <button type='submit' class='checkout-btn'>Proceed to Checkout</button>
                       </form>";
    } else {
        $cart_html .= "<p>Your cart is empty.</p>";
    }
    return $cart_html;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['ajax_place_order']) || isset($_POST['ajax_remove_one']))) {
    echo renderCart();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hotel Menu</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; }
.menu-container { display: flex; flex-wrap: wrap; gap: 20px; }
.food-item { border: 1px solid #ddd; padding: 10px; border-radius: 8px; min-width: 300px; max-width: 350px; background-color: #f9f9f9; text-align: center; }
.food-item h2 { margin: 5px 0; }
.food-item p.description { font-size: 0.9rem; margin: 5px 0; }
.food-item p.price { font-weight: bold; margin: 5px 0; }
.slideshow-container { position: relative; width: 100%; height: 200px; margin-bottom: 10px; overflow: hidden; }
.slideshow-container img { width: 100%; height: 200px; object-fit: cover; display: none; border-radius: 5px; }
.slideshow-container img.active { display: block; }
.prev, .next { cursor: pointer; position: absolute; top: 50%; transform: translateY(-50%); padding: 5px 10px; background-color: rgba(0,0,0,0.5); color: white; border-radius: 3px; user-select: none; }
.prev { left: 5px; }
.next { right: 5px; }
.place-order-btn { display: block; margin: 10px auto; padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
.place-order-btn:hover { background-color: #0069d9; }
.select-image { margin: 5px 0; padding: 5px; width: 100%; }
#cart-container { margin-top: 30px; }
.cart-item { margin: 5px 0; }
.checkout-btn { display: block; margin: 10px 0; padding: 8px 15px; background-color: #ffc107; color: black; border: none; border-radius: 5px; cursor: pointer; }
.checkout-btn:hover { background-color: #e0a800; }
</style>
</head>
<body>
<h1>Hotel Menu</h1>
<div class="menu-container">
<?php
$menu_sql = "SELECT * FROM menu_items ORDER BY menu_id";
$menu_result = $conn->query($menu_sql);
if (!$menu_result) die("Query failed: " . $conn->error);

while ($menu_row = $menu_result->fetch_assoc()) {
    $menu_id = $menu_row['menu_id'];
    $name = $menu_row['name'];
    $description = $menu_row['description'];
    $price = $menu_row['price'];

    echo "<div class='food-item'>";
    echo "<h2>$name</h2>";
    echo "<p class='description'>$description</p>";
    echo "<p class='price'>KSh $price</p>";

    // Fetch unique images
    $img_sql = "SELECT DISTINCT image_path FROM food_images WHERE menu_id='$menu_id'";
    $img_result = $conn->query($img_sql);
    $images = [];
    while ($img_row = $img_result->fetch_assoc()) {
        $images[] = $img_row['image_path'];
    }

    if (!empty($images)) {
        echo "<div class='slideshow-container' id='slideshow-$menu_id'>";
        foreach ($images as $index => $img) {
            $active_class = $index === 0 ? 'active' : '';
            $img_url = "uploads/foods/" . str_replace(' ', '%20', $img);
            echo "<img src='$img_url' class='$active_class' alt='$name'>";
        }
        echo "<span class='prev' onclick='changeSlide(-1, $menu_id)'>❮</span>";
        echo "<span class='next' onclick='changeSlide(1, $menu_id)'>❯</span>";
        echo "</div>";

        // Dropdown with short names
        echo "<select class='select-image' data-menu-id='$menu_id'>";
        $used_short_names = [];
        foreach ($images as $img) {
            $short_name = pathinfo($img, PATHINFO_FILENAME);
            if (!in_array($short_name, $used_short_names)) {
                echo "<option value='$img'>$short_name</option>";
                $used_short_names[] = $short_name;
            }
        }
        echo "</select>";
    }

    echo "<form class='place-order-form' data-menu-id='$menu_id' style='margin-top:5px;'>";
    echo "<input type='hidden' name='menu_id' value='$menu_id'>";
    echo "<input type='hidden' name='name' value='$name'>";
    echo "<input type='hidden' name='price' value='$price'>";
    echo "Quantity: <input type='number' name='quantity' value='1' min='1' style='width:50px; margin-left:5px;'>";
    echo "<input type='hidden' name='selected_image' value='".($images[0] ?? '')."'>";
    echo "<button type='submit' class='place-order-btn'>Place Order</button>";
    echo "</form>";
    echo "</div>";
}
?>
</div>

<div id="cart-container">
    <?= renderCart(); ?>
</div>
<a href="customer_portal.php" class="back-btn">Go Back to Customer Portal</a>
<script>
let slideIndex = {};
function showSlide(menuId, n){
    let container = document.getElementById('slideshow-' + menuId);
    if(!container) return;
    let slides = container.getElementsByTagName('img');
    if(!slideIndex[menuId]) slideIndex[menuId] = 0;
    if(n!==undefined) slideIndex[menuId] = n;
    if(slideIndex[menuId] >= slides.length) slideIndex[menuId] = 0;
    if(slideIndex[menuId] < 0) slideIndex[menuId] = slides.length - 1;
    for(let i=0;i<slides.length;i++) slides[i].classList.remove('active');
    slides[slideIndex[menuId]].classList.add('active');

    let form = document.querySelector(`.place-order-form[data-menu-id='${menuId}']`);
    let select = document.querySelector(`.select-image[data-menu-id='${menuId}']`);
    if(form && select) form.querySelector('input[name="selected_image"]').value = select.value;
}
function changeSlide(step, menuId){ showSlide(menuId, slideIndex[menuId]+step); }
document.querySelectorAll('.slideshow-container').forEach(container=>{
    let id = container.id.replace('slideshow-','');
    showSlide(parseInt(id),0);
});
document.querySelectorAll('.select-image').forEach(select=>{
    select.addEventListener('change', function(){
        let menuId = this.getAttribute('data-menu-id');
        let index = Array.from(this.options).indexOf(this.selectedOptions[0]);
        showSlide(menuId, index);
    });
});
document.querySelectorAll('.place-order-form').forEach(form => {
    form.addEventListener('submit', function(e){
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('ajax_place_order', true);
        fetch('menu.php',{ method:'POST', body:formData })
        .then(res=>res.text())
        .then(html=>{ document.getElementById('cart-container').innerHTML = html; });
    });
});
// Handle Remove buttons dynamically
document.addEventListener('submit', function(e){
    if(e.target.classList.contains('remove-form')){
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('ajax_remove_one', true);
        fetch('menu.php',{ method:'POST', body:formData })
        .then(res=>res.text())
        .then(html=>{ document.getElementById('cart-container').innerHTML = html; });
    }
});
</script>
</body>
</html>
