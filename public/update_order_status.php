<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['staff_id'])) {
    header('Location: admin_login.php');
    exit;
}

$order_id = intval($_GET['id'] ?? 0);
$status = $_GET['status'] ?? '';

if ($order_id && $status) {
    $stmt = $pdo->prepare("UPDATE `order` SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
}

header('Location: admin_dashboard.php');
exit;
?>
