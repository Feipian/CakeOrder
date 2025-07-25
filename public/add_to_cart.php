<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];
$product_id = intval($_POST['product_id']);
$quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

// Get or create cart
$stmt = $pdo->prepare("SELECT id FROM carts WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$cart = $stmt->fetch();

if (!$cart) {
    $stmt = $pdo->prepare("INSERT INTO carts (customer_id) VALUES (?)");
    $stmt->execute([$customer_id]);
    $cart_id = $pdo->lastInsertId();
} else {
    $cart_id = $cart['id'];
}

// Check if product already in cart
$stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
$stmt->execute([$cart_id, $product_id]);
$item = $stmt->fetch();

if ($item) {
    // Update quantity by adding the new quantity
    $new_quantity = $item['quantity'] + $quantity;
    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $stmt->execute([$new_quantity, $item['id']]);
} else {
    // Insert new item
    $stmt = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$cart_id, $product_id, $quantity]);
}

header('Location: index.php');
exit;
