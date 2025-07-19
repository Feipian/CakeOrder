<?php
session_start();

// Database connection (adjust credentials as needed)
$host = 'localhost';
$db   = 'cake_shop'; // Change to your DB name
$user = 'root';      // Change if not root
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$error = '';

if (isset($_SESSION['customer_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Both fields are required.';
    } else {
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
            $stmt = $pdo->prepare('SELECT id, name, password FROM customer WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                // Login success
                $_SESSION['customer_id'] = $user['id'];
                $_SESSION['customer_name'] = $user['name'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>Login - Cake Shop</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .login-form {
            max-width: 400px;
            margin: 3rem auto;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .login-form h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-form label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .login-form input {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-form .btn {
            width: 100%;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 1rem;
            text-align: center;
        }
        .success {
            color: #27ae60;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
<?php include_once("../templates/header.php"); ?>
<main>
    <form class="login-form" method="post" action="">
        <h2>Customer Login</h2>
        <?php if (isset($_GET['registered'])): ?>
            <div class="success">Registration successful! Please login.</div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn">Login</button>
        <p style="text-align:center; margin-top:1rem;">Don't have an account? <a href="register.php">Register</a></p>
    </form>
</main>
<?php include_once("../templates/footer.php"); ?>
</body>
</html> 