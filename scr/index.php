<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Beauty</title>
    <link rel="stylesheet" href="style.css?v=2">
</head>

<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="index.php" style="color: inherit; text-decoration: none; cursor: pointer;">Fresh Beauty</a></h1>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="#home" id="homeLink">Trang chủ</a></li>
                    <li><a href="#products" id="productsLink">Sản phẩm</a></li>
                    <li class="cart-menu-item">
                        <a href="#" id="cartMenuLink" class="cart-link">
                            🛒
                            <span class="cart-badge" id="cartBadge">0</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="header-actions">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm...">
                    <button id="searchBtn">🔍</button>
                </div>
                <div class="user-dropdown">
                    <button class="user-btn" id="userBtn" title="Tài khoản">👤</button>
                    <div class="user-dropdown-menu" id="userDropdownMenu" style="display: none;">
                        <!-- Menu khi chưa đăng nhập -->
                        <div class="dropdown-item" id="userLoginItem">
                            <span class="icon">🔑</span>
                            <span>Đăng nhập</span>
                        </div>
                        <div class="dropdown-item" id="userRegisterItem">
                            <span class="icon">📝</span>
                            <span>Đăng ký</span>
                        </div>
                        
                        <!-- Menu khi đã đăng nhập -->
                        <div class="dropdown-item" id="userProfileItem" style="display: none;">
                            <span class="icon">👤</span>
                            <span id="userNameDisplay">Tài khoản</span>
                        </div>
                        <div class="dropdown-item" id="userLogoutItem" style="display: none;">
                            <span class="icon">🚪</span>
                            <span>Đăng xuất</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content auth-modal">
            <span class="close" id="closeLoginModal">&times;</span>
            <h2>Đăng nhập</h2>
            <form id="loginForm" autocomplete="off">
                <div class="form-group">
                    <input type="text" id="loginUsername" name="username" placeholder="Tên đăng nhập" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <input type="password" id="loginPassword" name="password" placeholder="Mật khẩu" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn-submit">Đăng nhập</button>
            </form>
            <p class="auth-switch">Chưa có tài khoản? <a href="#" id="registerLink">Đăng ký</a></p>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content auth-modal">
            <span class="close" id="closeRegisterModal">&times;</span>
            <h2>Đăng ký tài khoản</h2>
            <form id="registerForm" autocomplete="off">
                <div class="form-group">
                    <input type="text" id="registerUsername" name="username" placeholder="Tên đăng nhập" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <input type="email" id="registerEmail" name="email" placeholder="Email" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <input type="password" id="registerPassword" name="password" placeholder="Mật khẩu (tối thiểu 6 ký tự)" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn-submit">Đăng ký</button>
            </form>
            <p class="auth-switch">Đã có tài khoản? <a href="#" id="loginLink">Đăng nhập</a></p>
        </div>
    </div>

    <!-- Cart Modal -->
    <div id="cartModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Giỏ hàng</h2>
            <div id="cartItems"></div>
            <div class="cart-total">
                <h3>Tổng cộng: <span id="totalPrice">0đ</span></h3>
                <button class="checkout-btn">Thanh toán</button>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkoutModal" class="modal">
        <div class="modal-content auth-modal">
            <span class="close" id="closeCheckoutModal">&times;</span>
            <h2>Thông tin thanh toán</h2>
            <form id="checkoutForm" autocomplete="off">
                <div class="form-group">
                    <input type="text" id="checkoutName" name="name" placeholder="Họ và tên" required>
                </div>
                <div class="form-group">
                    <input type="tel" id="checkoutPhone" name="phone" placeholder="Số điện thoại" pattern="[0-9]{10,11}" required>
                </div>
                <div class="form-group">
                    <input type="text" id="checkoutAddress" name="address" placeholder="Địa chỉ giao hàng" required>
                </div>
                <div class="checkout-summary">
                    <h3>Tổng tiền: <span id="checkoutTotal">0đ</span></h3>
                </div>
                <button type="submit" class="btn-submit">Xác nhận đặt hàng</button>
            </form>
        </div>
    </div>

    <!-- Product Detail Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content product-detail-modal">
            <span class="close" id="closeProductModal">&times;</span>
            <div class="product-detail-content">
                <div class="product-detail-image" id="modalProductImage"></div>
                <div class="product-detail-info">
                    <h2 id="modalProductName"></h2>
                    <p class="product-detail-category" id="modalProductCategory"></p>
                    <p class="product-detail-description" id="modalProductDescription"></p>
                    <p class="product-detail-price" id="modalProductPrice"></p>
                    <button class="add-to-cart" id="modalAddToCart">Thêm vào giỏ hàng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <p class="hero-subtitle">Chào mừng bạn đến với Fresh Beauty</p>
                <h1 class="hero-title">Bộ sưu tập mỹ phẩm dành riêng cho bạn</h1>
                <p class="hero-description">Cùng khám phá ngay nhé</p>
                <div class="hero-buttons">
                    <button class="btn-primary" onclick="document.getElementById('productsLink').click()">Khám phá
                        ngay</button>
                    <button class="btn-secondary" onclick="showFeaturedProducts()">Sản phẩm nổi
                        bật</button>
                </div>
            </div>
            <div class="hero-image">
                <div class="slideshow-container">
                    <div class="slide fade">
                        <img src="images/p00.jpg" alt="Fresh Beauty Banner 1">
                    </div>
                    <div class="slide fade">
                        <img src="images/p000.jpg" alt="Fresh Beauty Banner 2">
                    </div>
                    <div class="slide fade">
                        <img src="images/p0000.jpg" alt="Fresh Beauty Banner 3">
                    </div>

                    <!-- Nút điều hướng -->
                    <a class="prev" onclick="changeSlide(-1)">❮</a>
                    <a class="next" onclick="changeSlide(1)">❯</a>

                    <!-- Dots chỉ báo -->
                    <div class="dots-container">
                        <span class="dot" onclick="currentSlide(1)"></span>
                        <span class="dot" onclick="currentSlide(2)"></span>
                        <span class="dot" onclick="currentSlide(3)"></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section class="featured-products" id="featuredProducts" style="display: none;">
            <div class="container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Sản phẩm nổi bật</h2>
                    <button onclick="showHomePage()" style="padding: 8px 16px; background: #ff69b4; color: white; border: none; border-radius: 5px; cursor: pointer;">← Quay về trang chủ</button>
                </div>
                <div id="featuredProductGrid" class="product-grid"></div>
            </div>
        </section>

        <!-- All Products Page with Filter -->
        <div class="all-products-page" id="allProductsPage" style="display: none;">
            <div class="filter-sidebar">
                <h3>Bộ Lọc</h3>
                <div class="filter-section">
                    <h4>Danh Mục</h4>
                    <ul class="filter-list">
                        <li><a href="#" class="filter-link active" data-filter="all">Tất cả</a></li>
                        <li><a href="#" class="filter-link" data-filter="son">Son</a></li>
                        <li><a href="#" class="filter-link" data-filter="nuoc-hoa">Nước hoa</a></li>
                        <li><a href="#" class="filter-link" data-filter="kem-duong-am">Kem dưỡng ẩm</a></li>
                        <li><a href="#" class="filter-link" data-filter="kem-duong-trang">Kem dưỡng trắng</a></li>
                        <li><a href="#" class="filter-link" data-filter="sua-rua-mat">Sữa rửa mặt</a></li>
                        <li><a href="#" class="filter-link" data-filter="kem-chong-nang">Kem chống nắng</a></li>
                    </ul>
                </div>
            </div>
            <div class="products-content">
                <div id="allProductGrid" class="product-grid"></div>
            </div>
        </div>

        <!-- Category Products Section -->
        <section class="products" id="productsSection" style="display: none;">
            <div id="productGrid" class="product-grid"></div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Fresh Beauty</h3>
                    <p>Cửa hàng mỹ phẩm uy tín, chất lượng cao</p>
                </div>
                
                <div class="footer-section">
                    <h4>Thông tin liên hệ</h4>
                    <div class="contact-info">
                        <p><i class="icon">📍</i> Địa chỉ: Đường 5, TP. Trà Vinh</p>
                        <p><i class="icon">📞</i> Điện thoại: 017 568 4360</p>
                        <p><i class="icon">✉️</i> Email: freshbeauty@gmail.com</p>
                        <p><i class="icon">🕒</i> Giờ làm việc: 8:00 – 22:00 (Thứ 2 – CN)</p>
                    </div>
                </div>
                

                
                <div class="footer-section">
                    <h4>Hỗ trợ khách hàng</h4>
                    <ul class="footer-links">
                        <li><a href="#" onclick="showContactModal()">Liên hệ</a></li>
                        <li><a href="#" onclick="showPolicyModal()">Chính sách đổi trả</a></li>
                        <li><a href="#" onclick="showShippingModal()">Chính sách giao hàng</a></li>
                        <li><a href="#" onclick="showPrivacyModal()">Bảo mật thông tin</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Fresh Beauty. All rights reserved.</p>
                <p>Thiết kế bởi Fresh Beauty Team</p>
            </div>
        </div>
    </footer>

    <!-- Fresh Beauty Main JavaScript - Tổng hợp tất cả chức năng -->
    <script src="main.js?v=10"></script>
    
    <!-- User Dropdown Script -->
    <style>
        /* CSS cho user dropdown */
        .user-dropdown {
            position: relative;
        }
        
        .user-btn {
            background: none !important;
            border: none !important;
            font-size: 28px !important;
            cursor: pointer !important;
            padding: 12px !important;
            border-radius: 50% !important;
            transition: all 0.3s ease !important;
        }
        
        .user-btn:hover {
            background: rgba(255, 105, 180, 0.1) !important;
            transform: scale(1.1) !important;
        }
        
        .user-dropdown-menu {
            position: absolute !important;
            top: calc(100% + 10px) !important;
            right: 0 !important;
            background: white !important;
            border: 2px solid #ddd !important;
            border-radius: 8px !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2) !important;
            min-width: 180px !important;
            z-index: 9999 !important;
            padding: 8px 0 !important;
        }
        
        .user-dropdown-menu .dropdown-item {
            padding: 12px 16px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            transition: background 0.2s ease !important;
        }
        
        .user-dropdown-menu .dropdown-item:hover {
            background: #f5f5f5 !important;
        }
        
        .user-dropdown-menu .icon {
            font-size: 16px !important;
        }
    </style>
    
    <script>
        console.log('=== USER DROPDOWN SCRIPT LOADED ===');
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== DOM LOADED ===');
            
            setTimeout(function() {
                console.log('=== SETTING UP USER DROPDOWN ===');
                
                // Lấy các elements
                const userBtn = document.getElementById('userBtn');
                const userDropdownMenu = document.getElementById('userDropdownMenu');
                const userLoginItem = document.getElementById('userLoginItem');
                const userRegisterItem = document.getElementById('userRegisterItem');
                const userProfileItem = document.getElementById('userProfileItem');
                const userLogoutItem = document.getElementById('userLogoutItem');
                const userNameDisplay = document.getElementById('userNameDisplay');
                const loginModal = document.getElementById('loginModal');
                const registerModal = document.getElementById('registerModal');
                
                console.log('Elements found:', {
                    userBtn: !!userBtn,
                    userDropdownMenu: !!userDropdownMenu,
                    userLoginItem: !!userLoginItem,
                    userRegisterItem: !!userRegisterItem,
                    userProfileItem: !!userProfileItem,
                    userLogoutItem: !!userLogoutItem,
                    loginModal: !!loginModal,
                    registerModal: !!registerModal
                });
                
                // Function để cập nhật UI theo trạng thái đăng nhập
                function updateUserUI() {
                    const user = JSON.parse(localStorage.getItem('user') || 'null');
                    console.log('Current user:', user);
                    
                    if (user) {
                        // Đã đăng nhập
                        console.log('User logged in:', user.username);
                        userBtn.innerHTML = '👤 ' + user.username;
                        userBtn.title = 'Xin chào, ' + user.username;
                        
                        // Hiện menu đăng xuất, ẩn menu đăng nhập/đăng ký
                        if (userLoginItem) userLoginItem.style.display = 'none';
                        if (userRegisterItem) userRegisterItem.style.display = 'none';
                        if (userProfileItem) {
                            userProfileItem.style.display = 'flex';
                            if (userNameDisplay) userNameDisplay.textContent = user.username;
                        }
                        if (userLogoutItem) userLogoutItem.style.display = 'flex';
                    } else {
                        // Chưa đăng nhập
                        console.log('User not logged in');
                        userBtn.innerHTML = '👤';
                        userBtn.title = 'Tài khoản';
                        
                        // Hiện menu đăng nhập/đăng ký, ẩn menu đăng xuất
                        if (userLoginItem) userLoginItem.style.display = 'flex';
                        if (userRegisterItem) userRegisterItem.style.display = 'flex';
                        if (userProfileItem) userProfileItem.style.display = 'none';
                        if (userLogoutItem) userLogoutItem.style.display = 'none';
                    }
                }
                
                // Cập nhật UI lần đầu
                updateUserUI();
                
                if (userBtn && userDropdownMenu) {
                    console.log('=== ADDING CLICK HANDLER TO USER BUTTON ===');
                    
                    // Click vào nút user để toggle dropdown
                    userBtn.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('USER BUTTON CLICKED!');
                        
                        // Toggle dropdown
                        if (userDropdownMenu.style.display === 'block') {
                            userDropdownMenu.style.display = 'none';
                            console.log('Dropdown hidden');
                        } else {
                            userDropdownMenu.style.display = 'block';
                            console.log('Dropdown shown');
                        }
                    };
                    
                    // Click vào "Đăng nhập"
                    if (userLoginItem && loginModal) {
                        userLoginItem.onclick = function(e) {
                            e.preventDefault();
                            console.log('LOGIN ITEM CLICKED!');
                            
                            // Ẩn dropdown và mở modal đăng nhập
                            userDropdownMenu.style.display = 'none';
                            loginModal.style.display = 'block';
                            document.body.style.overflow = 'hidden';
                            console.log('Login modal opened');
                        };
                    }
                    
                    // Click vào "Đăng ký"
                    if (userRegisterItem && registerModal) {
                        userRegisterItem.onclick = function(e) {
                            e.preventDefault();
                            console.log('REGISTER ITEM CLICKED!');
                            
                            // Ẩn dropdown và mở modal đăng ký
                            userDropdownMenu.style.display = 'none';
                            registerModal.style.display = 'block';
                            document.body.style.overflow = 'hidden';
                            console.log('Register modal opened');
                        };
                    }
                    
                    // Click vào "Đăng xuất"
                    if (userLogoutItem) {
                        userLogoutItem.onclick = function(e) {
                            e.preventDefault();
                            console.log('LOGOUT ITEM CLICKED!');
                            
                            if (confirm('Bạn có chắc muốn đăng xuất?')) {
                                // Xóa thông tin user
                                localStorage.removeItem('user');
                                localStorage.removeItem('cart');
                                
                                // Cập nhật UI
                                updateUserUI();
                                
                                // Ẩn dropdown
                                userDropdownMenu.style.display = 'none';
                                
                                alert('Đã đăng xuất thành công!');
                                console.log('User logged out');
                            }
                        };
                    }
                    
                    // Đóng dropdown khi click bên ngoài
                    document.onclick = function(e) {
                        if (!userBtn.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                            userDropdownMenu.style.display = 'none';
                        }
                    };
                    
                    // Xử lý đóng modal (gộp chung cho tất cả modal)
                    function setupModalHandlers() {
                        const closeButtons = document.querySelectorAll('.close');
                        closeButtons.forEach(function(btn) {
                            btn.onclick = function() {
                                console.log('Close button clicked');
                                const modal = this.closest('.modal');
                                if (modal) {
                                    modal.style.display = 'none';
                                    document.body.style.overflow = 'auto';
                                }
                            };
                        });
                        
                        // Click bên ngoài modal để đóng
                        window.onclick = function(e) {
                            if (e.target.classList.contains('modal')) {
                                console.log('Clicked outside modal');
                                e.target.style.display = 'none';
                                document.body.style.overflow = 'auto';
                            }
                        };
                    }
                    
                    // Gọi function setup modal handlers
                    setupModalHandlers();
                    
                    // Xử lý form đăng nhập và đăng ký
                    function setupAuthForms() {
                        const loginForm = document.getElementById('loginForm');
                        const registerForm = document.getElementById('registerForm');
                        
                        if (loginForm) {
                            loginForm.onsubmit = async function(e) {
                                e.preventDefault();
                                console.log('Login form submitted');
                                
                                const username = document.getElementById('loginUsername').value.trim();
                                const password = document.getElementById('loginPassword').value;
                                
                                if (!username || !password) {
                                    alert('Vui lòng điền đầy đủ thông tin!');
                                    return;
                                }
                                
                                try {
                                    const response = await fetch('api/login.php', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ username, password })
                                    });
                                    
                                    const result = await response.json();
                                    console.log('Login result:', result);
                                    
                                    if (result.success) {
                                        localStorage.setItem('user', JSON.stringify(result.user));
                                        updateUserUI();
                                        loginModal.style.display = 'none';
                                        document.body.style.overflow = 'auto';
                                        alert('Đăng nhập thành công!');
                                        console.log('User logged in successfully');
                                    } else {
                                        alert(result.message || 'Đăng nhập thất bại!');
                                    }
                                } catch (error) {
                                    console.error('Login error:', error);
                                    alert('Có lỗi xảy ra khi đăng nhập!');
                                }
                            };
                        }
                        
                        if (registerForm) {
                            registerForm.onsubmit = async function(e) {
                                e.preventDefault();
                                console.log('Register form submitted');
                                
                                const username = document.getElementById('registerUsername').value.trim();
                                const email = document.getElementById('registerEmail').value.trim();
                                const password = document.getElementById('registerPassword').value;
                                
                                if (!username || !email || !password) {
                                    alert('Vui lòng điền đầy đủ thông tin!');
                                    return;
                                }
                                
                                try {
                                    const response = await fetch('api/register.php', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ 
                                            username, 
                                            email, 
                                            password,
                                            fullName: username
                                        })
                                    });
                                    
                                    const result = await response.json();
                                    console.log('Register result:', result);
                                    
                                    if (result.success) {
                                        alert('Đăng ký thành công! Vui lòng đăng nhập.');
                                        registerModal.style.display = 'none';
                                        loginModal.style.display = 'block';
                                        registerForm.reset();
                                    } else {
                                        alert(result.message || 'Đăng ký thất bại!');
                                    }
                                } catch (error) {
                                    console.error('Register error:', error);
                                    alert('Có lỗi xảy ra khi đăng ký!');
                                }
                            };
                        }
                    }
                    
                    // Gọi function setup auth forms
                    setupAuthForms();
                    
                    // Chuyển đổi giữa đăng nhập và đăng ký
                    function setupModalSwitching() {
                        const registerLink = document.getElementById('registerLink');
                        const loginLink = document.getElementById('loginLink');
                        
                        if (registerLink) {
                            registerLink.onclick = function(e) {
                                e.preventDefault();
                                console.log('Switch to register');
                                loginModal.style.display = 'none';
                                registerModal.style.display = 'block';
                            };
                        }
                        
                        if (loginLink) {
                            loginLink.onclick = function(e) {
                                e.preventDefault();
                                console.log('Switch to login');
                                registerModal.style.display = 'none';
                                loginModal.style.display = 'block';
                            };
                        }
                    }
                    
                    // Gọi function setup modal switching
                    setupModalSwitching();
                    
                    // Test function
                    window.testUserDropdown = function() {
                        console.log('=== MANUAL TEST ===');
                        userBtn.click();
                    };
                    
                    console.log('=== SETUP COMPLETE - Try: testUserDropdown() ===');
                    
                    // ===== FIX CHECKOUT BUTTON =====
                    console.log('=== SETTING UP CHECKOUT FIX ===');
                    
                    // Thêm event listener cho nút checkout
                    document.addEventListener('click', function(e) {
                        if (e.target.classList.contains('checkout-btn')) {
                            e.preventDefault();
                            console.log('=== CHECKOUT BUTTON CLICKED (FIXED) ===');
                            
                            // Lấy cart từ localStorage
                            const cart = JSON.parse(localStorage.getItem('cart')) || [];
                            console.log('Cart from localStorage:', cart);
                            
                            if (cart.length === 0) {
                                alert('Giỏ hàng trống!');
                                return;
                            }
                            
                            // Tính tổng tiền
                            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                            console.log('Total:', total);
                            
                            // Cập nhật tổng tiền trong checkout modal
                            const checkoutTotal = document.getElementById('checkoutTotal');
                            if (checkoutTotal) {
                                checkoutTotal.textContent = total.toLocaleString() + 'đ';
                            }
                            
                            // Hiển thị checkout modal
                            const checkoutModal = document.getElementById('checkoutModal');
                            const cartModal = document.getElementById('cartModal');
                            
                            if (checkoutModal) {
                                checkoutModal.style.display = 'block';
                                document.body.style.overflow = 'hidden';
                                console.log('Checkout modal opened');
                            }
                            
                            if (cartModal) {
                                cartModal.style.display = 'none';
                                console.log('Cart modal closed');
                            }
                        }
                    });
                    
                    // Xử lý form checkout
                    const checkoutForm = document.getElementById('checkoutForm');
                    if (checkoutForm) {
                        checkoutForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            console.log('=== CHECKOUT FORM SUBMITTED (FIXED) ===');
                            
                            const name = document.getElementById('checkoutName').value.trim();
                            const phone = document.getElementById('checkoutPhone').value.trim();
                            const address = document.getElementById('checkoutAddress').value.trim();
                            
                            if (!name || !phone || !address) {
                                alert('Vui lòng điền đầy đủ thông tin!');
                                return;
                            }
                            
                            if (!/^[0-9]{10,11}$/.test(phone)) {
                                alert('Số điện thoại không hợp lệ!');
                                return;
                            }
                            
                            const cart = JSON.parse(localStorage.getItem('cart')) || [];
                            const orderData = {
                                customer_name: name,
                                customer_phone: phone,
                                customer_address: address,
                                items: cart,
                                userId: null
                            };
                            
                            console.log('Sending order:', orderData);
                            
                            fetch('api/save_order.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(orderData)
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Order response:', data);
                                if (data.success) {
                                    alert('Đặt hàng thành công! Mã đơn: #' + data.orderId);
                                    localStorage.removeItem('cart');
                                    document.getElementById('checkoutModal').style.display = 'none';
                                    document.body.style.overflow = 'auto';
                                    checkoutForm.reset();
                                    
                                    // Cập nhật cart count nếu có function
                                    if (typeof updateCartCount === 'function') {
                                        updateCartCount();
                                    }
                                } else {
                                    alert('Lỗi: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Có lỗi xảy ra khi đặt hàng!');
                            });
                        });
                    }
                    
                } else {
                    console.log('=== ELEMENTS NOT FOUND ===');
                }
            }, 500);
        });
    </script>
</body>

</html>
