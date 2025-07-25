<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];
$stmt = $pdo->prepare("SELECT * FROM `order` WHERE customer_id = ? ORDER BY order_date DESC");
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include_once("../templates/header.php"); ?>
<main>
    <h1>My Orders</h1>
    <?php if (empty($orders)): ?>
        <p>You have no orders yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-block">
                <h2>Order #<?= $order['id'] ?> (<?= $order['order_date'] ?>) - Status: <?= htmlspecialchars($order['status']) ?></h2>
                <strong>Total: $<?= $order['total_price'] ?></strong>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt2 = $pdo->prepare("
                            SELECT oi.*, p.name
                            FROM order_item oi
                            JOIN products p ON oi.product_id = p.id
                            WHERE oi.order_id = ?
                        ");
                        $stmt2->execute([$order['id']]);
                        $items = $stmt2->fetchAll();
                        foreach ($items as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>$<?= $item['price'] ?></td>
                            <td>$<?= $subtotal ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
<?php include_once("../templates/footer.php"); ?>
</body>
</html>
