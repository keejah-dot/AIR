<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['full_name'], $data['phone'], $data['address'], $data['items'])) {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

// Insert order
$stmt = $conn->prepare("INSERT INTO orders (full_name, phone, address, total, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
$total = 0;
foreach($data['items'] as $item) { $total += $item['price'] * $item['quantity']; }
$stmt->execute([$data['full_name'], $data['phone'], $data['address'], $total]);
$orderId = $conn->lastInsertId();

// Insert order items
$stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach($data['items'] as $item) {
    $stmtItem->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
}

echo json_encode(['success' => true, 'order_id' => $orderId]);
?>
