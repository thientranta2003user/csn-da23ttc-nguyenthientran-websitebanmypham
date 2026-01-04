<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Footer - Fresh Beauty Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        h1 {
            color: #ff69b4;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ffe6f0;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #ff69b4;
        }
        
        textarea {
            height: 80px;
            resize: vertical;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #ff69b4;
            color: white;
        }
        
        .btn-primary:hover {
            background: #ff4da6;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .preview {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .preview h3 {
            color: #ff69b4;
            margin-bottom: 15px;
        }
        
        .preview-footer {
            background: linear-gradient(135deg, #ffd6e8 0%, #c9e4ff 100%);
            padding: 20px;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .preview-section h4 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .preview-section p {
            color: #666;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 Quản lý thông tin Footer</h1>
        
        <div id="alert"></div>
        
        <form id="footerForm">
            <div class="form-group">
                <label for="company_name">Tên công ty:</label>
                <input type="text" id="company_name" name="company_name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Mô tả:</label>
                <textarea id="description" name="description" placeholder="Mô tả ngắn về công ty"></textarea>
            </div>
            
            <div class="form-group">
                <label for="address">Địa chỉ:</label>
                <input type="text" id="address" name="address" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="working_hours">Giờ làm việc:</label>
                <input type="text" id="working_hours" name="working_hours" required>
            </div>
            
            <div class="form-group">
                <label for="copyright">Copyright:</label>
                <input type="text" id="copyright" name="copyright" required>
            </div>
            
            <div class="form-group">
                <label for="designed_by">Thiết kế bởi:</label>
                <input type="text" id="designed_by" name="designed_by">
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">💾 Lưu thông tin</button>
                <a href="admin.php" class="btn btn-secondary">← Quay lại Admin</a>
            </div>
        </form>
        
        <div class="preview">
            <h3>👀 Xem trước Footer</h3>
            <div class="preview-footer">
                <div class="preview-grid">
                    <div class="preview-section">
                        <h4 id="preview-company"></h4>
                        <p id="preview-description"></p>
                    </div>
                    <div class="preview-section">
                        <h4>Thông tin liên hệ</h4>
                        <p>📍 <span id="preview-address"></span></p>
                        <p>📞 <span id="preview-phone"></span></p>
                        <p>✉️ <span id="preview-email"></span></p>
                        <p>🕒 <span id="preview-hours"></span></p>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,105,180,0.2);">
                    <p id="preview-copyright"></p>
                    <p>Thiết kế bởi <span id="preview-designed"></span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load dữ liệu footer hiện tại
        async function loadFooterData() {
            try {
                const response = await fetch('api/footer_settings.php');
                const result = await response.json();
                
                if (result.success) {
                    const data = result.data;
                    
                    // Điền vào form
                    document.getElementById('company_name').value = data.company_name || '';
                    document.getElementById('description').value = data.description || '';
                    document.getElementById('address').value = data.address || '';
                    document.getElementById('phone').value = data.phone || '';
                    document.getElementById('email').value = data.email || '';
                    document.getElementById('working_hours').value = data.working_hours || '';
                    document.getElementById('copyright').value = data.copyright || '';
                    document.getElementById('designed_by').value = data.designed_by || '';
                    
                    // Cập nhật preview
                    updatePreview();
                }
            } catch (error) {
                console.error('Error loading footer data:', error);
            }
        }
        
        // Cập nhật preview
        function updatePreview() {
            document.getElementById('preview-company').textContent = document.getElementById('company_name').value;
            document.getElementById('preview-description').textContent = document.getElementById('description').value;
            document.getElementById('preview-address').textContent = document.getElementById('address').value;
            document.getElementById('preview-phone').textContent = document.getElementById('phone').value;
            document.getElementById('preview-email').textContent = document.getElementById('email').value;
            document.getElementById('preview-hours').textContent = document.getElementById('working_hours').value;
            document.getElementById('preview-copyright').textContent = document.getElementById('copyright').value;
            document.getElementById('preview-designed').textContent = document.getElementById('designed_by').value;
        }
        
        // Hiển thị thông báo
        function showAlert(message, type = 'success') {
            const alertDiv = document.getElementById('alert');
            alertDiv.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
            setTimeout(() => {
                alertDiv.innerHTML = '';
            }, 5000);
        }
        
        // Xử lý submit form
        document.getElementById('footerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('api/footer_settings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('✅ Cập nhật thông tin footer thành công!', 'success');
                } else {
                    showAlert('❌ Lỗi: ' + result.message, 'error');
                }
            } catch (error) {
                showAlert('❌ Có lỗi xảy ra: ' + error.message, 'error');
            }
        });
        
        // Cập nhật preview khi nhập liệu
        document.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('input', updatePreview);
        });
        
        // Load dữ liệu khi trang được tải
        document.addEventListener('DOMContentLoaded', loadFooterData);
    </script>
</body>
</html>