<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID sản phẩm']);
    exit;
}

$id = intval($data['id']);

// Kiểm tra xem sản phẩm có tồn tại không
$checkSql = "SELECT id FROM products WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy sản phẩm'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Xóa sản phẩm
$sql = "DELETE FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Xóa sản phẩm thành công'
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi xóa sản phẩm: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>