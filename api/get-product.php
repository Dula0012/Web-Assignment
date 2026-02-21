<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Product ID required']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $_GET['id']]);
$product = $stmt->fetch();

if ($product) {
    echo json_encode($product);
} else {
    echo json_encode(['error' => 'Product not found']);
}
?>
