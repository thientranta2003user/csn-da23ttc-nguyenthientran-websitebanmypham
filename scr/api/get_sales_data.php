<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

try {
    // Kiểm tra xem có đơn hàng nào không
    $checkSql = "SELECT COUNT(*) as count FROM orders";
    $checkResult = $conn->query($checkSql);
    $orderCount = $checkResult->fetch_assoc()['count'];
    
    // Nếu chưa có đơn hàng, tạo dữ liệu mẫu
    if ($orderCount == 0) {
        createSampleData($conn);
    }
    
    // Lấy tất cả đơn hàng với chi tiết sản phẩm
    $sql = "
        SELECT 
            o.id,
            o.customer_name,
            o.customer_phone,
            o.customer_address,
            o.total_amount,
            o.status,
            o.created_at,
            oi.product_id,
            oi.product_name,
            oi.quantity,
            oi.price
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        ORDER BY o.created_at DESC
    ";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    
    $orders = [];
    $orderItems = [];
    
    while ($row = $result->fetch_assoc()) {
        $orderId = $row['id'];
        
        // Nếu chưa có order này trong array
        if (!isset($orders[$orderId])) {
            $orders[$orderId] = [
                'id' => $row['id'],
                'customer_name' => $row['customer_name'],
                'customer_phone' => $row['customer_phone'],
                'customer_address' => $row['customer_address'],
                'total_amount' => $row['total_amount'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'items' => []
            ];
        }
        
        // Thêm item vào order (nếu có)
        if ($row['product_id']) {
            $orders[$orderId]['items'][] = [
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'quantity' => (int)$row['quantity'],
                'price' => (float)$row['price']
            ];
        }
    }
    
    // Chuyển từ associative array sang indexed array
    $ordersArray = array_values($orders);
    
    echo json_encode([
        'success' => true,
        'orders' => $ordersArray,
        'total_orders' => count($ordersArray),
        'message' => 'Dữ liệu được tải thành công'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
        'orders' => []
    ], JSON_UNESCAPED_UNICODE);
}

// Hàm tạo dữ liệu mẫu
function createSampleData($conn) {
    // Tạo đơn hàng mẫu với giá trị phù hợp (khoảng 1.5 triệu tổng)
    $sampleOrders = [
        ['Nguyễn Thị Lan', '0901234567', '123 Đường ABC, TP. Trà Vinh', 530000, 'completed', date('Y-m-d H:i:s', strtotime('-2 days'))],
        ['Trần Văn Nam', '0912345678', '456 Đường XYZ, TP. Trà Vinh', 380000, 'completed', date('Y-m-d H:i:s', strtotime('-1 day'))],
        ['Lê Thị Hoa', '0923456789', '789 Đường DEF, TP. Trà Vinh', 720000, 'completed', date('Y-m-d H:i:s')],
        ['Phạm Văn Đức', '0934567890', '321 Đường GHI, TP. Trà Vinh', 450000, 'pending', date('Y-m-d H:i:s')],
        ['Hoàng Thị Mai', '0945678901', '654 Đường JKL, TP. Trà Vinh', 280000, 'completed', date('Y-m-d H:i:s', strtotime('-3 days'))]
    ];
    
    // Insert orders
    foreach ($sampleOrders as $order) {
        $sql = "INSERT INTO orders (customer_name, customer_phone, customer_address, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdss", $order[0], $order[1], $order[2], $order[3], $order[4], $order[5]);
        $stmt->execute();
    }
    
    // Tạo order items mẫu
    $sampleItems = [
        // Đơn hàng 1 (530k)
        [1, 1, 'Son Môi Matte Đỏ', 1, 250000],
        [1, 5, 'Kem Dưỡng Ẩm Ngày', 1, 280000],
        
        // Đơn hàng 2 (380k) 
        [2, 7, 'Kem Dưỡng Trắng Vitamin C', 1, 380000],
        
        // Đơn hàng 3 (720k)
        [3, 2, 'Son Kem Lì Hồng', 1, 280000],
        [3, 3, 'Nước Hoa Hoa Hồng', 1, 440000],
        
        // Đơn hàng 4 (450k)
        [4, 3, 'Nước Hoa Hoa Hồng', 1, 450000],
        
        // Đơn hàng 5 (280k)
        [5, 1, 'Son Môi Matte Đỏ', 1, 250000],
        [5, 9, 'Sữa Rửa Mặt Tạo Bọt', 1, 30000]
    ];
    
    // Insert order items
    foreach ($sampleItems as $item) {
        $sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisid", $item[0], $item[1], $item[2], $item[3], $item[4]);
        $stmt->execute();
    }
}

$conn->close();
?>