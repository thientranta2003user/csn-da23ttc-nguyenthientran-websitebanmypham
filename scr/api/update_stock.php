<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id']) || !isset($data['stock'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Thiếu thông tin bắt buộc (id và stock)'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$id = intval($data['id']);
$stock = intval($data['stock']);

// Kiểm tra stock hợp lệ
if ($stock < 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Số lượng tồn kho phải >= 0'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra sản phẩm có tồn tại không
$checkSql = "SELECT id, name FROM products WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy sản phẩm với ID: ' . $id
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$product = $result->fetch_assoc();

// Cập nhật tồn kho
$sql = "UPDATE products SET stock = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $stock, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật tồn kho thành công',
            'product_name' => $product['name'],
            'new_stock' => $stock
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không có thay đổi nào (tồn kho đã là ' . $stock . ')'
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật tồn kho: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$checkStmt->close();
$conn->close();
?>