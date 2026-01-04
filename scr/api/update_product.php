<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id']) || !isset($data['name']) || !isset($data['category_id']) || !isset($data['price'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
    exit;
}

$id = intval($data['id']);
$name = $data['name'];
$category_id = intval($data['category_id']);
$description = $data['description'] ?? '';
$price = floatval($data['price']);
$image = $data['image'] ?? '';
$stock = intval($data['stock'] ?? 0);

$sql = "UPDATE products SET name = ?, category_id = ?, description = ?, price = ?, image = ?, stock = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisssii", $name, $category_id, $description, $price, $image, $stock, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không có thay đổi nào được thực hiện'
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật sản phẩm: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>