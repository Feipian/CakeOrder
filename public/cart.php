<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
$is_logged_in = isset($_SESSION['customer_id']);

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        $quantity = max(1, intval($quantity));
        if (isset($cart[$product_id])) {
            $cart[$product_id]['quantity'] = $quantity;
        }
    }
    $_SESSION['cart'] = $cart;
    header('Location: cart.php');
    exit;
}

// Handle remove item
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($cart[$remove_id]);
    $_SESSION['cart'] = $cart;
    header('Location: cart.php');
    exit;
}

// Handle clear cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header('Location: cart.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - Cake Shop</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        th, td { padding: 0.75rem; border: 1px solid #ddd; text-align: center; }
        .cart-actions { display: flex; justify-content: space-between; align-items: center; }
        .cart-empty { text-align: center; margin: 2rem 0; }
    </style>
</head>
<body>
<?php include_once("../templates/header.php"); ?>
<main>
    <h1>Your Shopping Cart</h1>
    <?php if (empty($cart)): ?>
        <div class="cart-empty">
            <p>Your cart is empty.</p>
            <a href="index.php" class="btn">Go Shopping</a>
        </div>
    <?php else: ?>
        <form method="post" action="cart.php">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($cart as $item): ?>
                        <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                        <tr data-product-id="<?= $item['product_id'] ?>">
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td class="item-price">$<?= htmlspecialchars($item['price']) ?></td>
                            <td>
                                <input type="number" name="quantities[<?= $item['product_id'] ?>]" value="<?= $item['quantity'] ?>" min="1" style="width:60px;" class="item-qty">
                            </td>
                            <td class="item-subtotal">$<?= $subtotal ?></td>
                            <td>
                                <a href="cart.php?remove=<?= $item['product_id'] ?>" onclick="return confirm('Remove this item?')">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-actions">

                <a href="cart.php?clear=1" class="btn" onclick="return confirm('Clear entire cart?')">Clear Cart</a>
                <div style="font-size:1.2rem;"><strong>Total: <span id="cart-total">$<?= $total ?></span></strong></div>
            </div>
        </form>
        <?php if ($is_logged_in): ?>
            <form action="checkout.php" method="post" style="text-align:right;">
                <button type="submit" class="btn">Proceed to Checkout</button>
            </form>
        <?php else: ?>
            <p style="color: #e74c3c; text-align:center;">
                Please <a href="login.php">Login</a> or <a href="register.php">Register</a> to buy cakes!
            </p>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php include_once("../templates/footer.php"); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateTotals() {
        let total = 0;
        document.querySelectorAll('tr[data-product-id]').forEach(function(row) {
            const price = parseFloat(row.querySelector('.item-price').textContent.replace('$', ''));
            const qtyInput = row.querySelector('.item-qty');
            const subtotalCell = row.querySelector('.item-subtotal');
            const quantity = parseInt(qtyInput.value) || 1;
            const subtotal = price * quantity;
            subtotalCell.textContent = '$' + subtotal;
            total += subtotal;
        });
        document.getElementById('cart-total').textContent = '$' + total;
    }

    document.querySelectorAll('.item-qty').forEach(function(input) {
        input.addEventListener('input', updateTotals);
    });
});
</script>
</body>
</html>