<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo 'Not logged in';
    exit;
}

$customer_id = $_SESSION['customer_id'];
$product_id = intval($_POST['product_id'] ?? 0);
$quantity = max(1, intval($_POST['quantity'] ?? 1));

// Get cart id
$stmt = $pdo->prepare("SELECT id FROM carts WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$cart = $stmt->fetch();
if (!$cart) {
    http_response_code(400);
    echo 'Cart not found';
    exit;
}
$cart_id = $cart['id'];

// Update quantity
$stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?");
$stmt->execute([$quantity, $cart_id, $product_id]);

echo 'ok';