<?php
// API để quản lý thông tin footer
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Lấy thông tin footer
            $stmt = $pdo->query("SELECT * FROM footer_settings ORDER BY id DESC LIMIT 1");
            $footer = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$footer) {
                // Tạo dữ liệu mặc định nếu chưa có
                $defaultFooter = [
                    'company_name' => 'Fresh Beauty',
                    'description' => 'Cửa hàng mỹ phẩm uy tín, chất lượng cao',
                    'address' => 'Đường 5, TP. Trà Vinh',
                    'phone' => '017 568 4360',
                    'email' => 'freshbeauty@gmail.com',
                    'working_hours' => '8:00 – 22:00 (Thứ 2 – CN)',
                    'copyright' => '2025 Fresh Beauty. All rights reserved.',
                    'designed_by' => 'Fresh Beauty Team'
                ];
                
                echo json_encode(['success' => true, 'data' => $defaultFooter]);
            } else {
                echo json_encode(['success' => true, 'data' => $footer]);
            }
            break;
            
        case 'POST':
        case 'PUT':
            // Cập nhật thông tin footer
            $input = json_decode(file_get_contents('php://input'), true);
            
            $company_name = $input['company_name'] ?? 'Fresh Beauty';
            $description = $input['description'] ?? '';
            $address = $input['address'] ?? '';
            $phone = $input['phone'] ?? '';
            $email = $input['email'] ?? '';
            $working_hours = $input['working_hours'] ?? '';
            $copyright = $input['copyright'] ?? '';
            $designed_by = $input['designed_by'] ?? '';
            
            // Kiểm tra xem đã có dữ liệu chưa
            $stmt = $pdo->query("SELECT COUNT(*) FROM footer_settings");
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                // Cập nhật
                $stmt = $pdo->prepare("UPDATE footer_settings SET 
                    company_name = ?, description = ?, address = ?, phone = ?, 
                    email = ?, working_hours = ?, copyright = ?, designed_by = ?, 
                    updated_at = NOW() 
                    WHERE id = (SELECT id FROM (SELECT id FROM footer_settings ORDER BY id DESC LIMIT 1) as temp)");
                
                $stmt->execute([$company_name, $description, $address, $phone, $email, $working_hours, $copyright, $designed_by]);
            } else {
                // Tạo mới
                $stmt = $pdo->prepare("INSERT INTO footer_settings 
                    (company_name, description, address, phone, email, working_hours, copyright, designed_by, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                
                $stmt->execute([$company_name, $description, $address, $phone, $email, $working_hours, $copyright, $designed_by]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin footer thành công']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>