<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['staff_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Get pending orders
$stmt = $pdo->prepare("
    SELECT o.*, c.name as customer_name 
    FROM `order` o 
    JOIN customer c ON o.customer_id = c.id 
    WHERE o.status = 'pending' 
    ORDER BY o.order_date DESC
");
$stmt->execute();
$pending_orders = $stmt->fetchAll();

// Get low stock materials
$stmt = $pdo->prepare("SELECT * FROM materials WHERE quantity < 10");
$stmt->execute();
$low_stock = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Cake Shop</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include_once("../templates/header.php"); ?>
<main>
    <h1>Admin Dashboard</h1>
    
    <h2>Pending Orders</h2>
    <?php if (empty($pending_orders)): ?>
        <p>No pending orders.</p>
    <?php else: ?>
        <?php foreach ($pending_orders as $order): ?>
            <div class="order-item">
                <h3>Order #<?= $order['id'] ?> - <?= $order['customer_name'] ?></h3>
                <p>Date: <?= $order['order_date'] ?></p>
                <p>Total: $<?= $order['total_price'] ?></p>
                <a href="view_order.php?id=<?= $order['id'] ?>" class="btn">View Details</a>
                <a href="update_order_status.php?id=<?= $order['id'] ?>&status=processing" class="btn">Start Processing</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <h2>Low Stock Materials</h2>
    <?php if (empty($low_stock)): ?>
        <p>All materials are well stocked.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($low_stock as $material): ?>
                <li><?= $material['name'] ?>: <?= $material['quantity'] ?> <?= $material['unit'] ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <div class="admin-actions">
        <a href="manage_products.php" class="btn">Manage Products</a>
        <a href="manage_materials.php" class="btn">Manage Materials</a>
        <a href="all_orders.php" class="btn">View All Orders</a>
    </div>
</main>
<?php include_once("../templates/footer.php"); ?>
</body>
</html>
