<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['staff_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Handle material updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $material_id = intval($_POST['material_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    
    if ($material_id && $quantity >= 0) {
        $stmt = $pdo->prepare("UPDATE materials SET quantity = ? WHERE id = ?");
        $stmt->execute([$quantity, $material_id]);
    }
}

// Get all materials
$stmt = $pdo->prepare("SELECT * FROM materials ORDER BY name");
$stmt->execute();
$materials = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>Manage Materials - Cake Shop</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include_once("../templates/header.php"); ?>
<main>
    <h1>Manage Materials</h1>
    <table>
        <thead>
            <tr>
                <th>Material</th>
                <th>Current Stock</th>
                <th>Unit</th>
                <th>Cost</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materials as $material): ?>
                <tr>
                    <td><?= htmlspecialchars($material['name']) ?></td>
                    <td><?= $material['quantity'] ?></td>
                    <td><?= $material['unit'] ?></td>
                    <td>$<?= $material['cost'] ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="material_id" value="<?= $material['id'] ?>">
                            <input type="number" name="quantity" value="<?= $material['quantity'] ?>" min="0" style="width:80px;">
                            <button type="submit" class="btn">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
<?php include_once("../templates/footer.php"); ?>
</body>
</html>
