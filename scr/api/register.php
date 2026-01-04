<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đăng ký']);
    exit;
}

$username = $data['username'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$email = isset($data['email']) ? $data['email'] : null;
$fullName = isset($data['fullName']) ? $data['fullName'] : null;

// Kiểm tra username đã tồn tại
$checkSql = "SELECT id FROM users WHERE username = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $username);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Tên đăng nhập đã tồn tại']);
    exit;
}

// Thêm user mới
$sql = "INSERT INTO users (username, password, email, full_name) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $username, $password, $email, $fullName);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Đăng ký thành công!'
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Đăng ký thất bại: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>
