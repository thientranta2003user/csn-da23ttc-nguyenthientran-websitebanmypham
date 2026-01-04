<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

// Xử lý logout nếu có tham số action=logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Xóa tất cả session
    session_unset();
    session_destroy();
    
    // Xóa cookie session nếu có
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Đăng xuất thành công!'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đăng nhập']);
    exit;
}

$username = $data['username'];
$password = $data['password'];

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'fullName' => $user['full_name']
            ]
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu không đúng']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại']);
}

$stmt->close();
$conn->close();
?>
