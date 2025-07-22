<?php
session_start();



$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$price = $_POST['price'];
$quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

// Database connection
$host = 'localhost';
$db = 'cake_shop'; // Change to your DB name
$user = 'root';      // Change if not root
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}


// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If product already in cart, increase quantity by the amount selected
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = [
        'product_id' => $product_id,
        'product_name' => $product_name,
        'price' => $price,
        'quantity' => $quantity
    ];
}

if (!isset($_SESSION['customer_id'])) {
    // 未登入，導回登入頁
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];
// 1. 取得/建立 cart
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

// 2. 檢查 cart_items 是否已有該商品
$stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
$stmt->execute([$cart_id, $product_id]);
$item = $stmt->fetch();

if ($item) {
    $new_quantity = $item['quantity'] + $quantity;
    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $stmt->execute([$new_quantity, $item['id']]);
} else {
    $stmt = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$cart_id, $product_id, $quantity]);
}

// Redirect back to cart
header('Location: cart.php');
exit;
