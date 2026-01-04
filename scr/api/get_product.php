<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID sản phẩm']);
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'product' => $product
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy sản phẩm'
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>