<?php
// Start session
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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $contact = trim($_POST['contact'] ?? '');

    // Basic validation
    if (!$name || !$email || !$password || !$confirm_password || !$contact) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
            // Check if email already exists
            $stmt = $pdo->prepare('SELECT id FROM customer WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email is already registered.';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                // Insert new customer
                $stmt = $pdo->prepare('INSERT INTO customer (name, email, password, contact) VALUES (?, ?, ?, ?)');
                $stmt->execute([$name, $email, $hashed_password, $contact]);
                // Redirect to login page
                header('Location: login.php?registered=1');
                exit;
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
    <title>Register - Cake Shop</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .register-form {
            max-width: 400px;
            margin: 3rem auto;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .register-form h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .register-form label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .register-form input {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .register-form .btn {
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
    <form class="register-form" method="post" action="">
        <h2>Customer Registration</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="contact">Contact</label>
        <input type="text" id="contact" name="contact" required value="<?= htmlspecialchars($_POST['contact'] ?? '') ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" class="btn">Register</button>
        <p style="text-align:center; margin-top:1rem;">Already have an account? <a href="login.php">Login</a></p>
    </form>
</main>
<?php include_once("../templates/footer.php"); ?>
</body>
</html> 