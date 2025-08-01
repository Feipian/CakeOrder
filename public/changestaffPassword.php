<?php
require_once 'db_connect.php';

// Your current plain text password
$plain_password = "admin123"; // Change this to your actual password

// Hash the password
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

// Update the staff record
$email = "admin@gmail.com"; // Change this to your staff email

try {
    $stmt = $pdo->prepare("UPDATE staff SET password = ? WHERE email = ?");
    $stmt->execute([$hashed_password, $email]);
    echo "Password updated successfully!<br>";
    echo "You can now login with the hashed password.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
