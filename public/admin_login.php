<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['staff_id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Both fields are required.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, name, password FROM staff WHERE email = ?');
            $stmt->execute([$email]);
            $staff = $stmt->fetch();
            
            // Debug: Check what we found
            if ($staff) {
                echo "Found staff: " . $staff['name'] . "<br>";
                echo "Password in DB: " . $staff['password'] . "<br>";
                echo "Password entered: " . $password . "<br>";
                echo "Password verify result: " . (password_verify($password, $staff['password']) ? 'TRUE' : 'FALSE') . "<br>";
            } else {
                echo "No staff found with email: " . $email . "<br>";
            }
            
            if ($staff && password_verify($password, $staff['password'])) {
                $_SESSION['staff_id'] = $staff['id'];
                $_SESSION['staff_name'] = $staff['name'];
                header('Location: admin_dashboard.php');
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
    <title>Staff Login - Cake Shop</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include_once("../templates/header.php"); ?>
<main>
    <form class="login-form" method="post" action="">
        <h2>Staff Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <button type="submit" class="btn">Login</button>
    </form>
</main>
<?php include_once("../templates/footer.php"); ?>
</body>
</html>
