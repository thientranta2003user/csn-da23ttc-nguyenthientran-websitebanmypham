<?php
// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kết nối database
require_once 'config.php';

// Lấy thống kê
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];

// Lấy danh sách sản phẩm với thông tin danh mục
$productsResult = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC
");

// Lấy danh sách danh mục
$categoriesResult = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = [];
while($cat = $categoriesResult->fetch_assoc()) {
    $categories[] = $cat;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Fresh Beauty</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #667eea;
            font-size: 36px;
        }

        .back-btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 18px;
        }

        .main-content {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #f0f0f0;
        }

        .section-header h2 {
            color: #333;
            font-size: 28px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 16px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 15px;
        }

        tbody tr:hover {
            background: #f8f9ff;
        }

        .user-id {
            color: #667eea;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: #d4edda;
            color: #155724;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 18px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .add-btn {
            padding: 12px 24px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .edit-btn, .delete-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 3px;
            font-size: 12px;
            transition: all 0.3s;
        }

        .edit-btn {
            background: #ffc107;
            color: #212529;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .edit-btn:hover, .delete-btn:hover {
            transform: translateY(-1px);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 28px;
            cursor: pointer;
            color: #aaa;
        }

        .close:hover {
            color: #667eea;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .save-btn, .cancel-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .save-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
        }

        .save-btn:hover, .cancel-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>👨‍💼 Admin Dashboard</h1>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="inventory.php" class="back-btn" style="background: #17a2b8;">📦 Quản lý kho hàng</a>
                <a href="sales_report.php" class="back-btn" style="background: #28a745;">📊 Báo cáo doanh thu</a>
                <a href="footer_management.php" class="back-btn" style="background: #ff69b4;">🎨 Quản lý Footer</a>
                <a href="index.php" class="back-btn">← Về trang chủ</a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Người dùng</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-value"><?php echo $totalProducts; ?></div>
                <div class="stat-label">Sản phẩm</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <div class="stat-value"><?php echo $totalOrders; ?></div>
                <div class="stat-label">Đơn hàng</div>
            </div>
        </div>

        <!-- Products Management -->
        <div class="main-content">
            <div class="section-header">
                <h2>📦 Quản lý sản phẩm</h2>
                <button class="add-btn" onclick="openAddModal()">+ Thêm sản phẩm mới</button>
            </div>

            <?php if ($productsResult->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($product = $productsResult->fetch_assoc()): ?>
                    <tr>
                        <td class="user-id">#<?php echo $product['id']; ?></td>
                        <td>
                            <?php if($product['image']): ?>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">📦</div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'Chưa phân loại'); ?></td>
                        <td style="color: #667eea; font-weight: bold;"><?php echo number_format($product['price']); ?>đ</td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <button class="edit-btn" onclick="editProduct(<?php echo $product['id']; ?>)">✏️ Sửa</button>
                            <button class="delete-btn" onclick="deleteProduct(<?php echo $product['id']; ?>)">🗑️ Xóa</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">
                <p>📭 Chưa có sản phẩm nào</p>
                <p style="margin-top: 10px; font-size: 16px;">Hãy thêm sản phẩm mới!</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Add/Edit Product Modal -->
        <div id="productModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 id="modalTitle">Thêm sản phẩm mới</h2>
                <form id="productForm">
                    <input type="hidden" id="productId" name="id">
                    
                    <div class="form-group">
                        <label>Tên sản phẩm:</label>
                        <input type="text" id="productName" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Danh mục:</label>
                        <select id="productCategory" name="category_id" required>
                            <option value="">Chọn danh mục</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Mô tả:</label>
                        <textarea id="productDescription" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Giá (VNĐ):</label>
                        <input type="number" id="productPrice" name="price" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Hình ảnh (URL):</label>
                        <input type="text" id="productImage" name="image" placeholder="images/product.jpg">
                    </div>
                    
                    <div class="form-group">
                        <label>Số lượng tồn kho:</label>
                        <input type="number" id="productStock" name="stock" min="0" value="0">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="save-btn">💾 Lưu</button>
                        <button type="button" class="cancel-btn" onclick="closeModal()">❌ Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Mở modal thêm sản phẩm
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Thêm sản phẩm mới';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('productModal').style.display = 'block';
        }

        // Đóng modal
        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        // Sửa sản phẩm
        function editProduct(id) {
            document.getElementById('modalTitle').textContent = 'Sửa sản phẩm';
            
            // Gọi API để lấy thông tin sản phẩm
            fetch(`api/get_product.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.product;
                        document.getElementById('productId').value = product.id;
                        document.getElementById('productName').value = product.name;
                        document.getElementById('productCategory').value = product.category_id;
                        document.getElementById('productDescription').value = product.description || '';
                        document.getElementById('productPrice').value = product.price;
                        document.getElementById('productImage').value = product.image || '';
                        document.getElementById('productStock').value = product.stock;
                        document.getElementById('productModal').style.display = 'block';
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi tải thông tin sản phẩm');
                });
        }

        // Xóa sản phẩm
        function deleteProduct(id) {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                fetch('api/delete_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Xóa sản phẩm thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa sản phẩm');
                });
            }
        }

        // Xử lý form submit
        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            const url = data.id ? 'api/update_product.php' : 'api/add_product.php';
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.id ? 'Cập nhật sản phẩm thành công!' : 'Thêm sản phẩm thành công!');
                    closeModal();
                    location.reload();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi lưu sản phẩm');
            });
        });

        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
