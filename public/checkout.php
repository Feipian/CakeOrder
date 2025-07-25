<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Get cart id
$stmt = $pdo->prepare("SELECT id FROM carts WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$cart = $stmt->fetch();
if (!$cart) {
    // No cart, nothing to checkout
    header('Location: cart.php');
    exit;
}
$cart_id = $cart['id'];

// Get cart items
$stmt = $pdo->prepare("
    SELECT ci.product_id, ci.quantity, p.price
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cart_id]);
$cart_items = $stmt->fetchAll();

if (!$cart_items) {
    // No items in cart
    header('Location: cart.php');
    exit;
}

// Calculate total price
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// 1. Create order
$stmt = $pdo->prepare("INSERT INTO `order` (customer_id, order_date, status, total_price) VALUES (?, NOW(), ?, ?)");
$stmt->execute([$customer_id, 'pending', $total_price]);
$order_id = $pdo->lastInsertId();

// 2. Add order items
$stmt = $pdo->prepare("INSERT INTO order_item (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach ($cart_items as $item) {
    $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
}

// 3. Clear cart
$stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
$stmt->execute([$cart_id]);

// If you leave the carts table record (do not delete it), you can potentially recover the cart items
// if the purchase fails or if you want to allow the user to "restore" their cart.
// This is useful for handling failed payments or interrupted checkouts.
// Only the cart_items are cleared after a successful order, so the cart remains for future use or recovery.



// Optionally, you can also delete the cart itself if you want
// $stmt = $pdo->prepare("DELETE FROM carts WHERE id = ?");
// $stmt->execute([$cart_id]);

// 4. Redirect to My Orders
header('Location: my_orders.php');
exit;
?>
