<?php
session_start();

require_once 'db_connect.php';



try {
    $stmt = $pdo->query('SELECT * FROM products');
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>Cake Shop</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <?php include_once("../templates/header.php"); ?>

    <main>
        <h1>Welcome to Cake Shop!</h1>
        <div class="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                        class="product-img">
                    <h2><?= htmlspecialchars($product['name']) ?></h2>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p class="price">$<?= htmlspecialchars($product['price']) ?></p>
                    <form method="post" action="add_to_cart.php" style="display:flex;gap:0.5rem;align-items:center;">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                        <input type="hidden" name="price" value="<?= $product['price'] ?>">
                        <input type="number" name="quantity" value="1" min="1" style="width:60px;">
                        <button type="submit" class="btn">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include_once("../templates/footer.php"); ?>
</body>

</html>