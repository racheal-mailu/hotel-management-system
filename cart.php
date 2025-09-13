<?php
session_start();

// Add item to cart
if (isset($_POST['add_to_cart'])) {
    $item_id = $_POST['menu_id'];
    $item_name = $_POST['name'];
    $item_price = $_POST['price'];
    $item_qty = $_POST['quantity'];

    // If cart doesn't exist, create one
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if item already exists in cart
    $item_found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['id'] == $item_id) {
            $cart_item['quantity'] += $item_qty; // increase quantity
            $item_found = true;
            break;
        }
    }

    // If not found, add new item
    if (!$item_found) {
        $_SESSION['cart'][] = [
            'id' => $item_id,
            'name' => $item_name,
            'price' => $item_price,
            'quantity' => $item_qty
        ];
    }
}

// Remove item from cart
if (isset($_GET['remove'])) {
    foreach ($_SESSION['cart'] as $key => $cart_item) {
        if ($cart_item['id'] == $_GET['remove']) {
            unset($_SESSION['cart'][$key]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 20px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; }
        th { background: #007bff; color: white; }
        .total { font-weight: bold; font-size: 1.2em; text-align: right; padding: 10px; }
        a { color: red; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .btn-checkout { background: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-checkout:hover { background: darkgreen; }
    </style>
</head>
<body>
    <h1>Your Cart</h1>

    <?php if (!empty($_SESSION['cart'])): ?>
        <table>
            <tr>
                <th>Food Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
            <?php
            $total = 0;
            foreach ($_SESSION['cart'] as $item):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td>Ksh <?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>Ksh <?php echo number_format($subtotal, 2); ?></td>
                <td><a href="cart.php?remove=<?php echo $item['id']; ?>">Remove</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <p class="total">Total: Ksh <?php echo number_format($total, 2); ?></p>
        <button class="btn-checkout">Proceed to Checkout</button>
    <?php else: ?>
        <p style="text-align:center;">Your cart is empty.</p>
    <?php endif; ?>
</body>
</html>
