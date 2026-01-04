-- ===================================================================
-- FRESH BEAUTY - HỆ THỐNG DATABASE HOÀN CHỈNH
-- Tạo ngày: 11/12/2024
-- Mô tả: File SQL tổng hợp tất cả bảng, trigger, query cho website bán mỹ phẩm
-- ===================================================================

-- ===== 1. TẠO DATABASE VÀ CÁC BẢNG CƠ BẢN =====

-- Tạo database
CREATE DATABASE IF NOT EXISTS fresh_beauty CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fresh_beauty;

-- Bảng danh mục sản phẩm
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng sản phẩm
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Bảng người dùng
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng đơn hàng (đã có đầy đủ cột khách hàng)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_address TEXT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Bảng chi tiết đơn hàng
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ===== 2. HỆ THỐNG BÁO CÁO DOANH THU =====

-- Bảng báo cáo doanh thu (tự động cập nhật từ orders)
CREATE TABLE IF NOT EXISTS sales_report (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_date DATE NOT NULL UNIQUE,
    total_orders INT DEFAULT 0,
    total_products_sold INT DEFAULT 0,
    total_revenue DECIMAL(12, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===== 3. HỆ THỐNG QUẢN LÝ KHO =====

-- Bảng lịch sử nhập/xuất kho
CREATE TABLE IF NOT EXISTS inventory_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    type ENUM('import', 'export', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    reason VARCHAR(255),
    reference_id INT NULL, -- ID đơn hàng nếu là xuất kho do bán
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by VARCHAR(100) DEFAULT 'system',
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Bảng thống kê kho tổng hợp
CREATE TABLE IF NOT EXISTS inventory_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL UNIQUE,
    initial_stock INT DEFAULT 0,        -- Số lượng ban đầu
    total_imported INT DEFAULT 0,       -- Tổng nhập kho
    total_exported INT DEFAULT 0,       -- Tổng xuất kho (bán)
    current_stock INT DEFAULT 0,        -- Tồn kho hiện tại
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ===== 4. THÊM DỮ LIỆU MẪU =====

-- Thêm dữ liệu mẫu cho categories
INSERT IGNORE INTO categories (name, slug, icon) VALUES
('Son', 'son', '💄'),
('Nước hoa', 'nuoc-hoa', '🌸'),
('Kem dưỡng ẩm', 'kem-duong-am', '🧴'),
('Kem dưỡng trắng', 'kem-duong-trang', '✨'),
('Sữa rửa mặt', 'sua-rua-mat', '🧼'),
('Kem chống nắng', 'kem-chong-nang', '☀️');

-- Thêm dữ liệu mẫu cho products
INSERT IGNORE INTO products (name, category_id, description, price, image, stock) VALUES
('Son Môi Matte Đỏ', 1, 'Son môi lâu trôi, màu đỏ quyến rũ', 250000, 'images/p01.jpg', 50),
('Son Kem Lì Hồng', 1, 'Son kem lì mịn môi, màu hồng tự nhiên', 280000, 'images/p02.jpg', 45),
('Nước Hoa Hoa Hồng', 2, 'Hương thơm nhẹ nhàng, lưu hương lâu', 450000, 'images/p03.jpg', 30),
('Nước Hoa Lavender', 2, 'Hương lavender thư giãn', 480000, 'images/p04.jpg', 25),
('Kem Dưỡng Ẩm Ngày', 3, 'Cấp ẩm cho da suốt cả ngày', 320000, 'images/p05.jpg', 60),
('Kem Dưỡng Ẩm Đêm', 3, 'Phục hồi da ban đêm', 350000, 'images/p06.jpg', 55),
('Kem Dưỡng Trắng Vitamin C', 4, 'Làm sáng da tự nhiên', 380000, 'images/p07.jpg', 40),
('Kem Dưỡng Trắng Niacinamide', 4, 'Giảm thâm nám hiệu quả', 420000, 'images/p08.jpg', 35),
('Sữa Rửa Mặt Tạo Bọt', 5, 'Làm sạch sâu, không khô da', 180000, 'images/p09.jpg', 70),
('Sữa Rửa Mặt Dịu Nhẹ', 5, 'Dành cho da nhạy cảm', 200000, 'images/p10.jpg', 65),
('Kem Chống Nắng SPF50', 6, 'Bảo vệ da khỏi tia UV', 280000, 'images/p11.jpg', 80),
('Kem Chống Nắng Dưỡng Ẩm', 6, 'Chống nắng và dưỡng ẩm', 320000, 'images/p12.jpg', 75);

-- Thêm dữ liệu mẫu cho sales_report (giả lập doanh thu 7 ngày qua)
INSERT IGNORE INTO sales_report (report_date, total_orders, total_products_sold, total_revenue) VALUES
(DATE_SUB(CURDATE(), INTERVAL 6 DAY), 2, 4, 180000),
(DATE_SUB(CURDATE(), INTERVAL 5 DAY), 3, 6, 320000),
(DATE_SUB(CURDATE(), INTERVAL 4 DAY), 1, 2, 150000),
(DATE_SUB(CURDATE(), INTERVAL 3 DAY), 4, 8, 420000),
(DATE_SUB(CURDATE(), INTERVAL 2 DAY), 2, 5, 280000),
(DATE_SUB(CURDATE(), INTERVAL 1 DAY), 3, 7, 380000),
(CURDATE(), 2, 3, 220000);

-- Thêm một số đơn hàng mẫu với giá trị phù hợp
INSERT IGNORE INTO orders (customer_name, customer_phone, customer_address, total_amount, status, created_at) VALUES
('Nguyễn Thị Lan', '0901234567', '123 Đường ABC, TP. Trà Vinh', 530000, 'completed', DATE_SUB(NOW(), INTERVAL 2 DAY)),
('Trần Văn Nam', '0912345678', '456 Đường XYZ, TP. Trà Vinh', 380000, 'completed', DATE_SUB(NOW(), INTERVAL 1 DAY)),
('Lê Thị Hoa', '0923456789', '789 Đường DEF, TP. Trà Vinh', 720000, 'completed', NOW()),
('Phạm Văn Đức', '0934567890', '321 Đường GHI, TP. Trà Vinh', 450000, 'pending', NOW()),
('Hoàng Thị Mai', '0945678901', '654 Đường JKL, TP. Trà Vinh', 280000, 'completed', DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Thêm chi tiết đơn hàng mẫu
INSERT IGNORE INTO order_items (order_id, product_id, product_name, quantity, price) VALUES
-- Đơn hàng 1 (530k)
(1, 1, 'Son Môi Matte Đỏ', 1, 250000),
(1, 5, 'Kem Dưỡng Ẩm Ngày', 1, 280000),

-- Đơn hàng 2 (380k) 
(2, 7, 'Kem Dưỡng Trắng Vitamin C', 1, 380000),

-- Đơn hàng 3 (720k)
(3, 2, 'Son Kem Lì Hồng', 1, 280000),
(3, 3, 'Nước Hoa Hoa Hồng', 1, 450000),

-- Đơn hàng 4 (450k)
(4, 3, 'Nước Hoa Hoa Hồng', 1, 450000),

-- Đơn hàng 5 (280k)
(5, 1, 'Son Môi Matte Đỏ', 1, 250000),
(5, 9, 'Sữa Rửa Mặt Tạo Bọt', 1, 180000);

-- ===== 5. TẠO CÁC TRIGGER TỰ ĐỘNG =====

-- Xóa trigger cũ nếu có
DROP TRIGGER IF EXISTS update_sales_report_after_order_insert;
DROP TRIGGER IF EXISTS update_inventory_summary_after_history_insert;
DROP TRIGGER IF EXISTS auto_export_inventory_after_order_item_insert;

-- Trigger cập nhật sales_report khi có đơn hàng mới
DELIMITER $

CREATE TRIGGER update_sales_report_after_order_insert
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    DECLARE order_date DATE;
    DECLARE products_count INT DEFAULT 0;
    
    SET order_date = DATE(NEW.created_at);
    
    -- Đếm số sản phẩm trong đơn hàng
    SELECT COALESCE(SUM(quantity), 0) INTO products_count
    FROM order_items 
    WHERE order_id = NEW.id;
    
    -- Cập nhật hoặc thêm mới vào sales_report
    INSERT INTO sales_report (report_date, total_orders, total_products_sold, total_revenue)
    VALUES (order_date, 1, products_count, NEW.total_amount)
    ON DUPLICATE KEY UPDATE
        total_orders = total_orders + 1,
        total_products_sold = total_products_sold + products_count,
        total_revenue = total_revenue + NEW.total_amount,
        updated_at = CURRENT_TIMESTAMP;
END$

-- Trigger cập nhật inventory_summary khi có thay đổi inventory_history
CREATE TRIGGER update_inventory_summary_after_history_insert
AFTER INSERT ON inventory_history
FOR EACH ROW
BEGIN
    -- Cập nhật hoặc tạo mới inventory_summary
    INSERT INTO inventory_summary (product_id, initial_stock, total_imported, total_exported, current_stock)
    VALUES (
        NEW.product_id, 
        CASE WHEN NEW.type = 'import' AND NEW.reason = 'initial_stock' THEN NEW.quantity ELSE 0 END,
        CASE WHEN NEW.type = 'import' THEN NEW.quantity ELSE 0 END,
        CASE WHEN NEW.type = 'export' THEN NEW.quantity ELSE 0 END,
        CASE 
            WHEN NEW.type = 'import' THEN NEW.quantity 
            WHEN NEW.type = 'export' THEN -NEW.quantity 
            ELSE NEW.quantity 
        END
    )
    ON DUPLICATE KEY UPDATE
        total_imported = total_imported + CASE WHEN NEW.type = 'import' THEN NEW.quantity ELSE 0 END,
        total_exported = total_exported + CASE WHEN NEW.type = 'export' THEN NEW.quantity ELSE 0 END,
        current_stock = current_stock + CASE 
            WHEN NEW.type = 'import' THEN NEW.quantity 
            WHEN NEW.type = 'export' THEN -NEW.quantity 
            ELSE NEW.quantity 
        END,
        last_updated = CURRENT_TIMESTAMP;
END$

-- Trigger tự động xuất kho khi có đơn hàng
CREATE TRIGGER auto_export_inventory_after_order_item_insert
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    -- Tự động tạo record xuất kho
    INSERT INTO inventory_history (product_id, type, quantity, reason, reference_id, created_by)
    VALUES (NEW.product_id, 'export', NEW.quantity, 'sold_via_order', NEW.order_id, 'system');
END$

DELIMITER ;

-- ===== 6. TẠO STORED PROCEDURES =====

-- Procedure đồng bộ stock từ inventory_summary về products
DELIMITER $

CREATE PROCEDURE sync_product_stock()
BEGIN
    UPDATE products p
    JOIN inventory_summary i ON p.id = i.product_id
    SET p.stock = i.current_stock;
END$

DELIMITER ;

-- ===== 7. KHỞI TẠO DỮ LIỆU KHO BAN ĐẦU =====

-- Khởi tạo dữ liệu kho ban đầu cho các sản phẩm hiện có
INSERT IGNORE INTO inventory_history (product_id, type, quantity, reason, created_by)
SELECT 
    id as product_id,
    'import' as type,
    stock as quantity,
    'initial_stock' as reason,
    'admin' as created_by
FROM products 
WHERE id NOT IN (SELECT DISTINCT product_id FROM inventory_history WHERE reason = 'initial_stock');

-- ===== 8. SỬA LỖI BẢNG ORDERS (NẾU CẦN) =====

-- Thêm các cột khách hàng nếu chưa có (cho database cũ)
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS customer_name VARCHAR(100) NOT NULL DEFAULT '';

ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS customer_phone VARCHAR(20) NOT NULL DEFAULT '';

ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS customer_address TEXT;

-- ===== 9. CÁC QUERY THỐNG KÊ DOANH THU =====

-- 9.1. DOANH THU THEO NGÀY (30 ngày gần nhất)
/*
SELECT 
    report_date as 'Ngày',
    total_orders as 'Tổng đơn hàng',
    total_products_sold as 'Tổng sản phẩm bán',
    FORMAT(total_revenue, 0) as 'Doanh thu (VNĐ)'
FROM sales_report 
WHERE report_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
ORDER BY report_date DESC;
*/

-- 9.2. DOANH THU THEO THÁNG
/*
SELECT 
    YEAR(report_date) as 'Năm',
    MONTH(report_date) as 'Tháng',
    SUM(total_orders) as 'Tổng đơn hàng',
    SUM(total_products_sold) as 'Tổng sản phẩm bán',
    FORMAT(SUM(total_revenue), 0) as 'Doanh thu (VNĐ)'
FROM sales_report 
GROUP BY YEAR(report_date), MONTH(report_date)
ORDER BY YEAR(report_date) DESC, MONTH(report_date) DESC;
*/

-- 9.3. THỐNG KÊ TỔNG QUAN
/*
SELECT 
    COUNT(*) as 'Tổng ngày có bán hàng',
    SUM(total_orders) as 'Tổng đơn hàng',
    SUM(total_products_sold) as 'Tổng sản phẩm đã bán',
    FORMAT(SUM(total_revenue), 0) as 'Tổng doanh thu (VNĐ)',
    FORMAT(AVG(total_revenue), 0) as 'Doanh thu TB/ngày (VNĐ)'
FROM sales_report;
*/

-- 9.4. TOP 5 NGÀY DOANH THU CAO NHẤT
/*
SELECT 
    report_date as 'Ngày',
    total_orders as 'Số đơn hàng',
    FORMAT(total_revenue, 0) as 'Doanh thu (VNĐ)'
FROM sales_report 
ORDER BY total_revenue DESC 
LIMIT 5;
*/

-- 9.5. SẢN PHẨM BÁN CHẠY NHẤT
/*
SELECT 
    p.name as 'Tên sản phẩm',
    c.name as 'Danh mục',
    SUM(oi.quantity) as 'Số lượng bán',
    FORMAT(SUM(oi.quantity * oi.price), 0) as 'Doanh thu (VNĐ)'
FROM order_items oi
JOIN products p ON oi.product_id = p.id
JOIN categories c ON p.category_id = c.id
GROUP BY p.id, p.name, c.name
ORDER BY SUM(oi.quantity) DESC
LIMIT 10;
*/

-- 9.6. DOANH THU THEO DANH MỤC
/*
SELECT 
    c.name as 'Danh mục',
    COUNT(DISTINCT oi.order_id) as 'Số đơn hàng',
    SUM(oi.quantity) as 'Số sản phẩm bán',
    FORMAT(SUM(oi.quantity * oi.price), 0) as 'Doanh thu (VNĐ)'
FROM order_items oi
JOIN products p ON oi.product_id = p.id
JOIN categories c ON p.category_id = c.id
GROUP BY c.id, c.name
ORDER BY SUM(oi.quantity * oi.price) DESC;
*/

-- ===== 10. CÁC QUERY QUẢN LÝ KHO =====

-- 10.1. Tình trạng kho hiện tại
/*
SELECT 
    p.name as 'Tên sản phẩm',
    c.name as 'Danh mục',
    i.initial_stock as 'Kho ban đầu',
    i.total_imported as 'Tổng nhập',
    i.total_exported as 'Tổng xuất (bán)',
    i.current_stock as 'Tồn kho hiện tại',
    CASE 
        WHEN i.current_stock <= 5 THEN 'Sắp hết'
        WHEN i.current_stock <= 10 THEN 'Ít'
        ELSE 'Đủ'
    END as 'Trạng thái'
FROM inventory_summary i
JOIN products p ON i.product_id = p.id
JOIN categories c ON p.category_id = c.id
ORDER BY i.current_stock ASC;
*/

-- 10.2. Sản phẩm cần nhập thêm (tồn kho thấp)
/*
SELECT 
    p.name as 'Sản phẩm',
    c.name as 'Danh mục',
    i.current_stock as 'Tồn kho hiện tại',
    i.total_exported as 'Đã bán',
    ROUND(i.total_exported / DATEDIFF(CURDATE(), DATE(MIN(ih.created_at))), 2) as 'TB bán/ngày'
FROM inventory_summary i
JOIN products p ON i.product_id = p.id
JOIN categories c ON p.category_id = c.id
JOIN inventory_history ih ON ih.product_id = p.id
WHERE i.current_stock <= 10
GROUP BY p.id, p.name, c.name, i.current_stock, i.total_exported
ORDER BY i.current_stock ASC;
*/

-- 10.3. Lịch sử nhập/xuất kho
/*
SELECT 
    p.name as 'Sản phẩm',
    ih.type as 'Loại',
    ih.quantity as 'Số lượng',
    ih.reason as 'Lý do',
    ih.created_at as 'Thời gian',
    ih.created_by as 'Người tạo'
FROM inventory_history ih
JOIN products p ON ih.product_id = p.id
ORDER BY ih.created_at DESC
LIMIT 50;
*/

-- ===== 11. CÁC QUERY KHÁCH HÀNG VÀ ĐỚN HÀNG =====

-- 11.1. XEM TẤT CẢ KHÁCH HÀNG VÀ ĐỚN HÀNG
/*
SELECT 
    o.customer_name as 'Tên khách hàng',
    o.customer_phone as 'Số điện thoại',
    o.customer_address as 'Địa chỉ',
    o.id as 'Mã đơn hàng',
    DATE(o.created_at) as 'Ngày đặt',
    FORMAT(o.total_amount, 0) as 'Tổng tiền (VNĐ)',
    o.status as 'Trạng thái'
FROM orders o
ORDER BY o.created_at DESC;
*/

-- 11.2. CHI TIẾT SẢN PHẨM TỪNG KHÁCH HÀNG ĐÃ MUA
/*
SELECT 
    o.customer_name as 'Tên khách hàng',
    o.customer_phone as 'Số điện thoại',
    o.id as 'Mã đơn hàng',
    p.name as 'Tên sản phẩm',
    c.name as 'Danh mục',
    oi.quantity as 'Số lượng',
    FORMAT(oi.price, 0) as 'Đơn giá (VNĐ)',
    FORMAT(oi.quantity * oi.price, 0) as 'Thành tiền (VNĐ)',
    DATE(o.created_at) as 'Ngày mua'
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN products p ON oi.product_id = p.id
JOIN categories c ON p.category_id = c.id
ORDER BY o.created_at DESC, o.customer_name;
*/

-- 11.3. TÌM KHÁCH HÀNG THEO SỐ ĐIỆN THOẠI
/*
SELECT 
    o.customer_name as 'Tên khách hàng',
    o.customer_phone as 'Số điện thoại',
    COUNT(o.id) as 'Số đơn hàng',
    SUM(o.total_amount) as 'Tổng chi tiêu',
    FORMAT(SUM(o.total_amount), 0) as 'Tổng chi tiêu (VNĐ)',
    MIN(DATE(o.created_at)) as 'Lần đầu mua',
    MAX(DATE(o.created_at)) as 'Lần cuối mua'
FROM orders o
WHERE o.customer_phone = '0358874187'  -- Thay số điện thoại cần tìm
GROUP BY o.customer_name, o.customer_phone;
*/

-- 11.4. TOP KHÁCH HÀNG MUA NHIỀU NHẤT
/*
SELECT 
    o.customer_name as 'Tên khách hàng',
    o.customer_phone as 'Số điện thoại',
    COUNT(o.id) as 'Số đơn hàng',
    SUM(oi.quantity) as 'Tổng sản phẩm mua',
    FORMAT(SUM(o.total_amount), 0) as 'Tổng chi tiêu (VNĐ)',
    FORMAT(AVG(o.total_amount), 0) as 'Trung bình/đơn (VNĐ)'
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.customer_name, o.customer_phone
ORDER BY SUM(o.total_amount) DESC
LIMIT 10;
*/

-- 11.5. KHÁCH HÀNG THÂN THIẾT (MUA >= 3 LẦN)
/*
SELECT 
    o.customer_name as 'Tên khách hàng',
    o.customer_phone as 'Số điện thoại',
    COUNT(o.id) as 'Số lần mua',
    FORMAT(SUM(o.total_amount), 0) as 'Tổng chi tiêu (VNĐ)',
    FORMAT(AVG(o.total_amount), 0) as 'Trung bình/đơn (VNĐ)',
    MIN(DATE(o.created_at)) as 'Khách hàng từ',
    MAX(DATE(o.created_at)) as 'Mua gần nhất'
FROM orders o
GROUP BY o.customer_name, o.customer_phone
HAVING COUNT(o.id) >= 3
ORDER BY COUNT(o.id) DESC, SUM(o.total_amount) DESC;
*/

-- ===== 12. CÁC QUERY TÌM KIẾM NHANH =====

-- Tìm khách hàng theo tên
-- SELECT * FROM orders WHERE customer_name LIKE '%tên_cần_tìm%';

-- Tìm khách hàng theo số điện thoại
-- SELECT * FROM orders WHERE customer_phone = 'số_điện_thoại';

-- Tìm đơn hàng trong khoảng thời gian
-- SELECT * FROM orders WHERE created_at BETWEEN '2024-12-01' AND '2024-12-31';

-- Tìm khách hàng mua sản phẩm cụ thể
-- SELECT DISTINCT o.customer_name, o.customer_phone 
-- FROM orders o 
-- JOIN order_items oi ON o.id = oi.order_id 
-- JOIN products p ON oi.product_id = p.id 
-- WHERE p.name LIKE '%tên_sản_phẩm%';

-- ===== 13. HOÀN TẤT THIẾT LẬP =====

-- Hiển thị thông báo hoàn thành
SELECT 
    'Database Fresh Beauty đã được tạo hoàn chỉnh!' as 'Trạng thái',
    COUNT(DISTINCT table_name) as 'Số bảng đã tạo'
FROM information_schema.tables 
WHERE table_schema = 'fresh_beauty';

-- Hiển thị danh sách bảng
SELECT 
    table_name as 'Tên bảng',
    table_rows as 'Số dòng dữ liệu'
FROM information_schema.tables 
WHERE table_schema = 'fresh_beauty'
ORDER BY table_name;

-- ===================================================================
-- KẾT THÚC FILE SQL TỔNG HỢP
-- Hướng dẫn sử dụng:
-- 1. Chạy toàn bộ file này trong MySQL để tạo database hoàn chỉnh
-- 2. Bỏ comment (/*...*/) các query cần sử dụng để chạy thống kê
-- 3. File này thay thế tất cả các file SQL riêng lẻ trước đó
-- ===================================================================

-- ===== 5. BẢNG QUẢN LÝ FOOTER =====

-- Bảng footer_settings để quản lý thông tin footer
CREATE TABLE IF NOT EXISTS footer_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL DEFAULT 'Fresh Beauty',
    description TEXT,
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(100),
    working_hours VARCHAR(100),
    copyright VARCHAR(255),
    designed_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Thêm dữ liệu mặc định cho footer
INSERT IGNORE INTO footer_settings (company_name, description, address, phone, email, working_hours, copyright, designed_by) VALUES
('Fresh Beauty', 'Cửa hàng mỹ phẩm uy tín, chất lượng cao', 'Đường 5, TP. Trà Vinh', '017 568 4360', 'freshbeauty@gmail.com', '8:00 – 22:00 (Thứ 2 – CN)', '2025 Fresh Beauty. All rights reserved.', 'Fresh Beauty Team');