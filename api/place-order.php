<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['customer']) || !isset($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    $customer = $data['customer'];
    $items = $data['items'];
    $totalAmount = $data['totalAmount'];
    
    $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, total_amount, status) VALUES (:name, :email, :phone, :address, :total, 'Pending')");
    $stmt->execute([
        ':name' => $customer['name'],
        ':email' => $customer['email'],
        ':phone' => $customer['phone'],
        ':address' => $customer['address'],
        ':total' => $totalAmount
    ]);
    
    $orderId = $pdo->lastInsertId();
    
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (:orderId, :productId, :productName, :quantity, :price)");
    
    foreach ($items as $item) {
        $itemStmt->execute([
            ':orderId' => $orderId,
            ':productId' => $item['id'],
            ':productName' => $item['name'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price']
        ]);
        
        $updateStock = $pdo->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :id");
        $updateStock->execute([
            ':quantity' => $item['quantity'],
            ':id' => $item['id']
        ]);
    }
    
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Order placed successfully!', 'orderId' => $orderId]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Failed to place order. Please try again.']);
}
?>
