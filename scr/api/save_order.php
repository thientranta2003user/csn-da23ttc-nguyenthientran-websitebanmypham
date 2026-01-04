<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

// Nhận dữ liệu từ request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['items']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
    exit;
}

// Validate customer info
if (!isset($data['customer_name']) || !isset($data['customer_phone']) || !isset($data['customer_address'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin khách hàng']);
    exit;
}

$userId = isset($data['userId']) ? $data['userId'] : null;
$customerName = $data['customer_name'];
$customerPhone = $data['customer_phone'];
$customerAddress = $data['customer_address'];
$items = $data['items'];
$totalAmount = 0;

// Tính tổng tiền
foreach ($items as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Thêm đơn hàng với thông tin khách hàng
    $sql = "INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, total_amount, status) VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssd", $userId, $customerName, $customerPhone, $customerAddress, $totalAmount);
    $stmt->execute();
    $orderId = $conn->insert_id;
    
    // Thêm chi tiết đơn hàng
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($items as $item) {
        $stmt->bind_param("iiid", $orderId, $item['id'], $item['quantity'], $item['price']);
        $stmt->execute();
        
        // Cập nhật số lượng tồn kho
        $updateSql = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ii", $item['quantity'], $item['id']);
        $updateStmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Đặt hàng thành công!',
        'orderId' => $orderId
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    echo json_encode([
        'success' => false, 
        'message' => 'Đặt hàng thất bại: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
