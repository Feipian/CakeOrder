<?php
session_start();
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
        <!-- 範例蛋糕商品，實際可從資料庫撈取 -->
        <div class="product-card">
            <img src="assets/chocolate.png" alt="Chocolate Cake" class="product-img">
            <h2>Chocolate Cake</h2>
            <p>Rich and moist chocolate cake.</p>
            <p class="price">$350</p>
        </div>
        <div class="product-card">
            <img src="assets/Strawberry.jpg" alt="Strawberry Cake" class="product-img">
            <h2>Strawberry Cake</h2>
            <p>Fresh strawberries with cream.</p>
            <p class="price">$400</p>
        </div>
        <!-- 更多蛋糕可依需求新增 -->
    </div>
</main>

<?php include_once("../templates/footer.php"); ?>
</body>
</html>