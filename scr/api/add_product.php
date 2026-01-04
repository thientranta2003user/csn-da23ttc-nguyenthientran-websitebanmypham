<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['name']) || !isset($data['category_id']) || !isset($data['price'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
    exit;
}

$name = $data['name'];
$category_id = intval($data['category_id']);
$description = $data['description'] ?? '';
$price = floatval($data['price']);
$image = $data['image'] ?? '';
$stock = intval($data['stock'] ?? 0);

$sql = "INSERT INTO products (name, category_id, description, price, image, stock) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisssi", $name, $category_id, $description, $price, $image, $stock);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Thêm sản phẩm thành công',
        'id' => $conn->insert_id
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi thêm sản phẩm: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>