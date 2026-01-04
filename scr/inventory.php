<?php
session_start();
require_once 'config.php';

// Kiểm tra đăng nhập admin (có thể bỏ qua nếu chưa có hệ thống admin)
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý kho hàng - Fresh Beauty</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .inventory-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .page-header {
            background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #17a2b8;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-size: 16px;
        }
        
        .inventory-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .table-header {
            background: #17a2b8;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .filter-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .filter-select {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .filter-select option {
            color: #333;
        }
        
        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        
        .product-inventory-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid #17a2b8;
            transition: all 0.3s;
        }
        
        .product-inventory-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .product-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .product-details h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .product-category {
            color: #666;
            font-size: 14px;
        }
        
        .stock-info {
            display: flex;
            justify-content: center;
        }
        
        .stock-item {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            flex: 1;
        }
        
        .stock-current {
            background: #e7f3ff;
            color: #0066cc;
        }
        
        .stock-low {
            background: #fff3cd;
            color: #856404;
        }
        
        .stock-out {
            background: #f8d7da;
            color: #721c24;
        }
        
        .stock-number {
            font-size: 24px;
            font-weight: bold;
            display: block;
        }
        
        .stock-label {
            font-size: 12px;
            margin-top: 5px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-update {
            flex: 1;
            padding: 8px 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-update:hover {
            background: #218838;
        }
        
        .back-btn {
            display: inline-block;
            padding: 12px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .inventory-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-controls {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="inventory-container">
        <a href="admin.php" class="back-btn">← Quay về Admin</a>
        
        <div class="page-header">
            <h1>📦 Quản lý kho hàng</h1>
            <p>Theo dõi tồn kho Fresh Beauty</p>
        </div>
        
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-number" id="totalProducts">-</div>
                <div class="stat-label">Tổng sản phẩm</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-number" id="totalStock">-</div>
                <div class="stat-label">Tổng tồn kho</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⚠️</div>
                <div class="stat-number" id="lowStock">-</div>
                <div class="stat-label">Sắp hết hàng</div>
            </div>
        </div>
        
        <div class="inventory-table">
            <div class="table-header">
                <h3>📋 Chi tiết tồn kho</h3>
                <div class="filter-controls">
                    <select class="filter-select" id="stockFilter">
                        <option value="all">Tất cả</option>
                        <option value="in-stock">Còn hàng</option>
                        <option value="low-stock">Sắp hết</option>
                        <option value="out-stock">Hết hàng</option>
                    </select>
                    <select class="filter-select" id="categoryFilter">
                        <option value="all">Tất cả danh mục</option>
                    </select>
                </div>
            </div>
            
            <div id="loadingState" class="loading">
                <h3>🔄 Đang tải dữ liệu kho hàng...</h3>
            </div>
            
            <div class="inventory-grid" id="inventoryGrid" style="display: none;">
                <!-- Sản phẩm sẽ được load bằng JavaScript -->
            </div>
        </div>
    </div>

    <script>
        let products = [];
        let categories = {};
        
        // Load dữ liệu khi trang được tải
        document.addEventListener('DOMContentLoaded', function() {
            loadInventoryData();
            setupFilters();
        });
        
        // Load dữ liệu từ API
        async function loadInventoryData() {
            try {
                const response = await fetch('api/get_products.php');
                const data = await response.json();
                
                products = data.map(product => ({
                    ...product,
                    stock: product.stock || 0
                }));
                
                // Tạo danh sách categories
                categories = {};
                products.forEach(product => {
                    if (!categories[product.category]) {
                        categories[product.category] = product.categoryName || product.category;
                    }
                });
                
                updateStats();
                updateCategoryFilter();
                displayInventory();
                
                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('inventoryGrid').style.display = 'grid';
                
            } catch (error) {
                console.error('Error loading inventory:', error);
                document.getElementById('loadingState').innerHTML = '<h3 style="color: red;">❌ Có lỗi xảy ra khi tải dữ liệu</h3>';
            }
        }
        
        // Cập nhật thống kê
        function updateStats() {
            const totalProducts = products.length;
            const totalStock = products.reduce((sum, p) => sum + p.stock, 0);
            const lowStock = products.filter(p => p.stock > 0 && p.stock <= 5).length;
            
            document.getElementById('totalProducts').textContent = totalProducts;
            document.getElementById('totalStock').textContent = totalStock.toLocaleString();
            document.getElementById('lowStock').textContent = lowStock;
        }
        
        // Cập nhật filter danh mục
        function updateCategoryFilter() {
            const categoryFilter = document.getElementById('categoryFilter');
            
            // Xóa options cũ (trừ "Tất cả danh mục")
            while (categoryFilter.children.length > 1) {
                categoryFilter.removeChild(categoryFilter.lastChild);
            }
            
            // Thêm options mới
            Object.entries(categories).forEach(([slug, name]) => {
                const option = document.createElement('option');
                option.value = slug;
                option.textContent = name;
                categoryFilter.appendChild(option);
            });
        }
        
        // Hiển thị inventory
        function displayInventory(filteredProducts = null) {
            const productsToShow = filteredProducts || products;
            const grid = document.getElementById('inventoryGrid');
            grid.innerHTML = '';
            
            productsToShow.forEach(product => {
                const card = document.createElement('div');
                card.className = 'product-inventory-card';
                
                // Xác định trạng thái stock
                let stockClass = 'stock-current';
                let stockStatus = 'Còn hàng';
                
                if (product.stock === 0) {
                    stockClass = 'stock-out';
                    stockStatus = 'Hết hàng';
                } else if (product.stock <= 5) {
                    stockClass = 'stock-low';
                    stockStatus = 'Sắp hết';
                }
                
                card.innerHTML = `
                    <div class="product-info">
                        <img src="${product.image}" alt="${product.name}" class="product-image" onerror="this.src='https://via.placeholder.com/60x60?text=No+Image'">
                        <div class="product-details">
                            <h4>${product.name}</h4>
                            <div class="product-category">${product.categoryName || product.category}</div>
                        </div>
                    </div>
                    
                    <div class="stock-info">
                        <div class="stock-item ${stockClass}">
                            <span class="stock-number">${product.stock}</span>
                            <div class="stock-label">${stockStatus}</div>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="btn-update" onclick="updateStock(${product.id})">📝 Cập nhật tồn kho</button>
                    </div>
                `;
                
                grid.appendChild(card);
            });
        }
        
        // Setup filters
        function setupFilters() {
            const stockFilter = document.getElementById('stockFilter');
            const categoryFilter = document.getElementById('categoryFilter');
            
            stockFilter.addEventListener('change', applyFilters);
            categoryFilter.addEventListener('change', applyFilters);
        }
        
        // Áp dụng filters
        function applyFilters() {
            const stockFilter = document.getElementById('stockFilter').value;
            const categoryFilter = document.getElementById('categoryFilter').value;
            
            let filtered = products;
            
            // Filter theo stock
            if (stockFilter !== 'all') {
                filtered = filtered.filter(product => {
                    switch (stockFilter) {
                        case 'in-stock': return product.stock > 5;
                        case 'low-stock': return product.stock > 0 && product.stock <= 5;
                        case 'out-stock': return product.stock === 0;
                        default: return true;
                    }
                });
            }
            
            // Filter theo category
            if (categoryFilter !== 'all') {
                filtered = filtered.filter(product => product.category === categoryFilter);
            }
            
            displayInventory(filtered);
        }
        
        // Cập nhật stock
        function updateStock(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) {
                alert('Không tìm thấy sản phẩm!');
                return;
            }
            
            const currentStock = product.stock || 0;
            const newStock = prompt(
                `Cập nhật tồn kho cho "${product.name}":\n\n` +
                `Tồn kho hiện tại: ${currentStock}\n` +
                `Nhập số lượng mới (>= 0):`, 
                currentStock
            );
            
            if (newStock === null) return; // User cancelled
            
            // Validate input
            const stockNumber = parseInt(newStock);
            if (isNaN(stockNumber)) {
                alert('Vui lòng nhập một số hợp lệ!');
                return;
            }
            
            if (stockNumber < 0) {
                alert('Số lượng tồn kho phải >= 0!');
                return;
            }
            
            if (stockNumber === currentStock) {
                alert('Số lượng mới giống số lượng hiện tại!');
                return;
            }
            
            // Confirm update
            const confirmMsg = `Xác nhận cập nhật tồn kho:\n\n` +
                              `Sản phẩm: ${product.name}\n` +
                              `Từ: ${currentStock} → ${stockNumber}\n\n` +
                              `Bạn có chắc chắn?`;
            
            if (confirm(confirmMsg)) {
                updateProductStock(productId, stockNumber);
            }
        }
        
        // Cập nhật stock trong database
        async function updateProductStock(productId, newStock) {
            try {
                const response = await fetch('api/update_stock.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: productId,
                        stock: newStock
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Cập nhật local data
                    const product = products.find(p => p.id === productId);
                    if (product) {
                        product.stock = newStock;
                    }
                    
                    // Refresh display
                    updateStats();
                    applyFilters();
                    
                    alert(`Cập nhật tồn kho thành công!\nSản phẩm: ${result.product_name}\nTồn kho mới: ${result.new_stock}`);
                } else {
                    alert('Có lỗi xảy ra: ' + (result.message || 'Vui lòng thử lại'));
                }
            } catch (error) {
                console.error('Error updating stock:', error);
                alert('Có lỗi xảy ra khi cập nhật tồn kho! Vui lòng kiểm tra kết nối mạng.');
            }
        }
        
        // Format giá tiền
        function formatPrice(price) {
            const numPrice = parseInt(price) || 0;
            return numPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + 'đ';
        }
    </script>
</body>
</html>