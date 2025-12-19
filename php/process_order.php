<?php
session_start();
header('Content-Type: application/json');
require_once 'db_config.php';

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!isset($data['user_id']) || !isset($data['customer_name']) || !isset($data['email']) || !isset($data['address']) || !isset($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, email, address, total_amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssd", $data['user_id'], $data['customer_name'], $data['email'], $data['address'], $data['total']);
    $stmt->execute();
    
    $order_id = $conn->insert_id;
    
    // Insert order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
    
    foreach ($data['items'] as $item) {
        $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Order placed successfully',
        'order_id' => $order_id
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>