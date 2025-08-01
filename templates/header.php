
<header>
    <nav style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="index.php"><strong>Cake Shop</strong></a>
        </div>
        <div>
            <?php if (isset($_SESSION['customer_id'])): ?>
                <a href="cart.php" class="btn">Cart</a>
                <a href="my_orders.php" class="btn">My Orders</a>
                <a href="logout.php" class="btn">Logout</a>
            <?php elseif (isset($_SESSION['staff_id'])): ?>
                <a href="admin_dashboard.php" class="btn">Dashboard</a>
                <a href="manage_products.php" class="btn">Products</a>
                <a href="manage_materials.php" class="btn">Materials</a>
                <a href="logout.php" class="btn">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn">Customer Login</a>
                <a href="register.php" class="btn">Register</a>
                <a href="admin_login.php" class="btn">Staff Login</a>
            <?php endif; ?>
        </div>
    </nav>
    <hr>
</header>