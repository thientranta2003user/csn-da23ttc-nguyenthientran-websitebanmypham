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
    <title>Báo cáo doanh thu - Fresh Beauty</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .sales-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .page-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .date-filter {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .filter-group label {
            font-weight: 600;
            color: #333;
        }
        
        .filter-input {
            padding: 10px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .filter-input:focus {
            border-color: #28a745;
            outline: none;
        }
        
        .btn-filter {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 20px;
        }
        
        .btn-filter:hover {
            background: #218838;
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
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-size: 16px;
        }
        
        .reports-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .report-section {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .section-header {
            background: #28a745;
            color: white;
            padding: 20px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .section-content {
            padding: 20px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            transition: background 0.3s;
        }
        
        .order-item:hover {
            background: #f8f9fa;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-info {
            flex: 1;
        }
        
        .order-id {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .order-details {
            color: #666;
            font-size: 14px;
        }
        
        .order-amount {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        
        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .product-item:last-child {
            border-bottom: none;
        }
        
        .product-name {
            font-weight: 600;
            color: #333;
        }
        
        .product-stats {
            text-align: right;
            color: #666;
            font-size: 14px;
        }
        
        .product-revenue {
            font-weight: bold;
            color: #28a745;
            margin-top: 3px;
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
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .reports-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="sales-container">
        <a href="admin.php" class="back-btn">← Quay về Admin</a>
        
        <div class="page-header">
            <h1>📊 Báo cáo doanh thu</h1>
            <p>Theo dõi doanh số cửa hàng Fresh Beauty</p>
        </div>
        
        <div class="date-filter">
            <h3>🗓️ Lọc theo thời gian</h3>
            <div class="filter-row">
                <div class="filter-group">
                    <label>Từ ngày:</label>
                    <input type="date" class="filter-input" id="startDate">
                </div>
                <div class="filter-group">
                    <label>Đến ngày:</label>
                    <input type="date" class="filter-input" id="endDate">
                </div>
                <div class="filter-group">
                    <label>Nhanh:</label>
                    <select class="filter-input" id="quickFilter">
                        <option value="">Chọn khoảng thời gian</option>
                        <option value="today">Hôm nay</option>
                        <option value="yesterday">Hôm qua</option>
                        <option value="this-week">Tuần này</option>
                        <option value="last-week">Tuần trước</option>
                        <option value="this-month">Tháng này</option>
                        <option value="last-month">Tháng trước</option>
                    </select>
                </div>
            </div>
            <button class="btn-filter" onclick="applyDateFilter()">🔍 Lọc dữ liệu</button>
        </div>
        
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-number" id="totalRevenue">-</div>
                <div class="stat-label">Tổng doanh thu</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-number" id="totalOrders">-</div>
                <div class="stat-label">Tổng đơn hàng</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-number" id="avgOrderValue">-</div>
                <div class="stat-label">Giá trị TB/đơn</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🛍️</div>
                <div class="stat-number" id="totalProducts">-</div>
                <div class="stat-label">Sản phẩm bán</div>
            </div>
        </div>
        
        <div id="loadingState" class="loading">
            <h3>🔄 Đang tải báo cáo doanh thu...</h3>
        </div>
        
        <div class="reports-grid" id="reportsGrid" style="display: none;">
            <div class="report-section">
                <div class="section-header">📋 Đơn hàng gần đây</div>
                <div class="section-content" id="recentOrders">
                    <!-- Đơn hàng sẽ được load bằng JavaScript -->
                </div>
            </div>
            
            <div class="report-section">
                <div class="section-header">🏆 Sản phẩm bán chạy</div>
                <div class="section-content" id="topProducts">
                    <!-- Sản phẩm sẽ được load bằng JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let allOrders = [];
        let filteredOrders = [];
        
        // Load dữ liệu khi trang được tải
        document.addEventListener('DOMContentLoaded', function() {
            setupDateFilters();
            loadSalesData();
        });
        
        // Setup date filters
        function setupDateFilters() {
            const today = new Date();
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            const quickFilter = document.getElementById('quickFilter');
            
            // Set default dates (7 ngày gần đây thay vì cả tháng)
            const sevenDaysAgo = new Date(today);
            sevenDaysAgo.setDate(today.getDate() - 7);
            startDate.value = sevenDaysAgo.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];
            
            // Quick filter handler
            quickFilter.addEventListener('change', function() {
                const value = this.value;
                if (!value) return;
                
                const dates = getQuickFilterDates(value);
                startDate.value = dates.start;
                endDate.value = dates.end;
                
                applyDateFilter();
            });
        }
        
        // Get dates for quick filters
        function getQuickFilterDates(filter) {
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            
            switch (filter) {
                case 'today':
                    return {
                        start: today.toISOString().split('T')[0],
                        end: today.toISOString().split('T')[0]
                    };
                case 'yesterday':
                    return {
                        start: yesterday.toISOString().split('T')[0],
                        end: yesterday.toISOString().split('T')[0]
                    };
                case 'this-week':
                    const startOfWeek = new Date(today);
                    startOfWeek.setDate(today.getDate() - today.getDay());
                    return {
                        start: startOfWeek.toISOString().split('T')[0],
                        end: today.toISOString().split('T')[0]
                    };
                case 'last-week':
                    const lastWeekEnd = new Date(today);
                    lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
                    const lastWeekStart = new Date(lastWeekEnd);
                    lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                    return {
                        start: lastWeekStart.toISOString().split('T')[0],
                        end: lastWeekEnd.toISOString().split('T')[0]
                    };
                case 'this-month':
                    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    return {
                        start: firstDay.toISOString().split('T')[0],
                        end: today.toISOString().split('T')[0]
                    };
                case 'last-month':
                    const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
                    return {
                        start: lastMonthStart.toISOString().split('T')[0],
                        end: lastMonthEnd.toISOString().split('T')[0]
                    };
                default:
                    return {
                        start: today.toISOString().split('T')[0],
                        end: today.toISOString().split('T')[0]
                    };
            }
        }
        
        // Load dữ liệu từ database
        async function loadSalesData() {
            try {
                // Load orders
                const ordersResponse = await fetch('api/get_sales_data.php');
                const ordersData = await ordersResponse.json();
                
                if (ordersData.success) {
                    allOrders = ordersData.orders || [];
                    applyDateFilter();
                } else {
                    throw new Error(ordersData.message || 'Không thể tải dữ liệu');
                }
                
            } catch (error) {
                console.error('Error loading sales data:', error);
                document.getElementById('loadingState').innerHTML = '<h3 style="color: red;">❌ Có lỗi xảy ra khi tải dữ liệu</h3>';
            }
        }
        
        // Áp dụng filter theo ngày
        function applyDateFilter() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            if (!startDate || !endDate) {
                alert('Vui lòng chọn ngày bắt đầu và kết thúc');
                return;
            }
            
            // Filter orders theo ngày
            filteredOrders = allOrders.filter(order => {
                const orderDate = order.created_at.split(' ')[0]; // Lấy phần ngày
                return orderDate >= startDate && orderDate <= endDate;
            });
            
            updateStats();
            displayReports();
            
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('reportsGrid').style.display = 'grid';
        }
        
        // Cập nhật thống kê
        function updateStats() {
            const totalRevenue = filteredOrders.reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
            const totalOrders = filteredOrders.length;
            const avgOrderValue = totalOrders > 0 ? totalRevenue / totalOrders : 0;
            
            // Tính tổng sản phẩm bán
            const totalProducts = filteredOrders.reduce((sum, order) => {
                return sum + (order.items ? order.items.reduce((itemSum, item) => itemSum + item.quantity, 0) : 0);
            }, 0);
            
            document.getElementById('totalRevenue').textContent = formatPrice(totalRevenue);
            document.getElementById('totalOrders').textContent = totalOrders.toLocaleString();
            document.getElementById('avgOrderValue').textContent = formatPrice(avgOrderValue);
            document.getElementById('totalProducts').textContent = totalProducts.toLocaleString();
        }
        
        // Hiển thị báo cáo
        function displayReports() {
            displayRecentOrders();
            displayTopProducts();
        }
        
        // Hiển thị đơn hàng gần đây
        function displayRecentOrders() {
            const container = document.getElementById('recentOrders');
            container.innerHTML = '';
            
            if (filteredOrders.length === 0) {
                container.innerHTML = '<div class="empty-state">Không có đơn hàng nào trong khoảng thời gian này</div>';
                return;
            }
            
            // Sắp xếp theo ngày mới nhất
            const recentOrders = [...filteredOrders]
                .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                .slice(0, 10);
            
            recentOrders.forEach(order => {
                const orderDiv = document.createElement('div');
                orderDiv.className = 'order-item';
                
                const orderDate = new Date(order.created_at).toLocaleDateString('vi-VN');
                const orderTime = new Date(order.created_at).toLocaleTimeString('vi-VN');
                
                orderDiv.innerHTML = `
                    <div class="order-info">
                        <div class="order-id">Đơn hàng #${order.id}</div>
                        <div class="order-details">
                            ${order.customer_name} • ${orderDate} ${orderTime}
                        </div>
                    </div>
                    <div class="order-amount">${formatPrice(order.total_amount)}</div>
                `;
                
                container.appendChild(orderDiv);
            });
        }
        
        // Hiển thị sản phẩm bán chạy
        function displayTopProducts() {
            const container = document.getElementById('topProducts');
            container.innerHTML = '';
            
            if (filteredOrders.length === 0) {
                container.innerHTML = '<div class="empty-state">Không có dữ liệu sản phẩm</div>';
                return;
            }
            
            // Tính toán sản phẩm bán chạy
            const productStats = {};
            
            filteredOrders.forEach(order => {
                if (order.items) {
                    order.items.forEach(item => {
                        if (!productStats[item.product_name]) {
                            productStats[item.product_name] = {
                                name: item.product_name,
                                quantity: 0,
                                revenue: 0
                            };
                        }
                        
                        productStats[item.product_name].quantity += item.quantity;
                        productStats[item.product_name].revenue += item.quantity * item.price;
                    });
                }
            });
            
            // Sắp xếp theo doanh thu
            const topProducts = Object.values(productStats)
                .sort((a, b) => b.revenue - a.revenue)
                .slice(0, 10);
            
            if (topProducts.length === 0) {
                container.innerHTML = '<div class="empty-state">Không có dữ liệu sản phẩm</div>';
                return;
            }
            
            topProducts.forEach(product => {
                const productDiv = document.createElement('div');
                productDiv.className = 'product-item';
                
                productDiv.innerHTML = `
                    <div class="product-name">${product.name}</div>
                    <div class="product-stats">
                        <div>Đã bán: ${product.quantity}</div>
                        <div class="product-revenue">${formatPrice(product.revenue)}</div>
                    </div>
                `;
                
                container.appendChild(productDiv);
            });
        }
        
        // Format giá tiền
        function formatPrice(price) {
            const numPrice = parseInt(price) || 0;
            return numPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + 'đ';
        }
    </script>
</body>
</html>