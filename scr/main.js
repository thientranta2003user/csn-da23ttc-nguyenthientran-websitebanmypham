// ===================================================================
// FRESH BEAUTY - JAVASCRIPT TỔNG HỢP
// Tạo ngày: 11/12/2024
// Mô tả: File JS tổng hợp tất cả chức năng cho website bán mỹ phẩm
// ===================================================================

console.log('Fresh Beauty Main.js loaded');

// ===== 1. BIẾN GLOBAL =====
let products = [];
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let isSearchMode = false;
let slideIndex = 1;
let slideTimer;

// ===== 2. UTILITY FUNCTIONS =====

// Format giá tiền
function formatPrice(price) {
    // Chuyển về số nguyên để loại bỏ phần thập phân .00
    const numPrice = parseInt(price) || 0;
    return numPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + 'đ';
}

// Lấy tên danh mục
function getCategoryName(category) {
    const categories = {
        'son': 'Son',
        'nuoc-hoa': 'Nước hoa',
        'kem-duong-am': 'Kem dưỡng ẩm',
        'kem-duong-trang': 'Kem dưỡng trắng',
        'sua-rua-mat': 'Sữa rửa mặt',
        'kem-chong-nang': 'Kem chống nắng'
    };
    return categories[category] || category;
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// ===== 3. SLIDESHOW FUNCTIONALITY =====

function showSlides(n) {
    const slides = document.getElementsByClassName('slide');
    const dots = document.getElementsByClassName('dot');

    if (!slides.length) return;

    if (n > slides.length) { slideIndex = 1; }
    if (n < 1) { slideIndex = slides.length; }

    for (let i = 0; i < slides.length; i++) {
        slides[i].classList.remove('active');
    }

    for (let i = 0; i < dots.length; i++) {
        dots[i].classList.remove('active');
    }

    slides[slideIndex - 1].classList.add('active');
    dots[slideIndex - 1].classList.add('active');
}

function changeSlide(n) {
    clearInterval(slideTimer);
    showSlides(slideIndex += n);
    autoSlide();
}

function currentSlide(n) {
    clearInterval(slideTimer);
    showSlides(slideIndex = n);
    autoSlide();
}

function autoSlide() {
    slideTimer = setInterval(function () {
        changeSlide(1);
    }, 5000);
}

// ===== 4. PRODUCTS FUNCTIONALITY =====

// Load products từ API
function loadProductsFromAPI() {
    fetch('api/get_products.php')
        .then(response => response.json())
        .then(data => {
            products = data.map(product => ({
                id: product.id,
                name: product.name,
                category: product.categorySlug,
                categoryName: product.category,
                price: product.priceRaw,
                priceRaw: product.priceRaw,
                image: product.image,
                description: product.description || '',
                stock: product.stock || 0
            }));
            console.log('Products loaded from API:', products.length);
            
            // Trigger event để các phần khác biết products đã load xong
            window.dispatchEvent(new CustomEvent('productsLoaded'));
        })
        .catch(error => {
            console.error('Error loading products:', error);
            products = [];
        });
}

// Hiển thị sản phẩm
function displayProducts(productsToShow, containerId = 'productGrid') {
    const productGrid = document.getElementById(containerId);
    if (!productGrid) {
        console.error(containerId + ' not found');
        return;
    }
    
    productGrid.innerHTML = '';

    productsToShow.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.style.cursor = 'pointer';
        productCard.innerHTML = `
            <div class="product-image">
                <img src="${product.image}" alt="${product.name}" onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
            </div>
            <h3>${product.name}</h3>
            <p>Danh mục: ${product.categoryName || getCategoryName(product.category)}</p>
            <div class="product-price">${formatPrice(product.price)}</div>
            <button class="add-to-cart" onclick="event.stopPropagation(); addToCart(${product.id})">Thêm vào giỏ</button>
        `;

        // Click vào card để xem chi tiết
        productCard.addEventListener('click', function () {
            showProductModal(product);
        });

        productGrid.appendChild(productCard);
    });
}

// Hiển thị modal sản phẩm
function showProductModal(product) {
    console.log('Showing product modal for:', product.name);
    
    const productModal = document.getElementById('productModal');
    const modalProductImage = document.getElementById('modalProductImage');
    const modalProductName = document.getElementById('modalProductName');
    const modalProductCategory = document.getElementById('modalProductCategory');
    const modalProductDescription = document.getElementById('modalProductDescription');
    const modalProductPrice = document.getElementById('modalProductPrice');
    const modalAddToCart = document.getElementById('modalAddToCart');
    
    if (!productModal) {
        console.error('Product modal not found');
        return;
    }
    
    // Cập nhật nội dung modal
    if (modalProductImage) {
        modalProductImage.innerHTML = `<img src="${product.image}" alt="${product.name}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">`;
    }
    
    if (modalProductName) {
        modalProductName.textContent = product.name;
    }
    
    if (modalProductCategory) {
        modalProductCategory.textContent = 'Danh mục: ' + (product.categoryName || getCategoryName(product.category));
    }
    
    if (modalProductDescription) {
        modalProductDescription.textContent = product.description || 'Sản phẩm chất lượng cao, được nhiều khách hàng tin dùng.';
    }
    
    if (modalProductPrice) {
        modalProductPrice.textContent = formatPrice(product.price);
    }
    
    if (modalAddToCart) {
        modalAddToCart.onclick = function() {
            addToCart(product.id);
            productModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        };
    }
    
    // Hiển thị modal
    productModal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// ===== 5. CART FUNCTIONALITY =====

function updateCartCount() {
    const cartBadge = document.getElementById('cartBadge');
    if (cartBadge) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartBadge.textContent = totalItems;
        cartBadge.style.display = totalItems === 0 ? 'none' : 'flex';
    }
}

function addToCart(productId) {
    const product = products.find(p => p.id == productId);
    if (!product) {
        alert('Không tìm thấy sản phẩm!');
        return;
    }
    
    const existingItem = cart.find(item => item.id == productId);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ ...product, quantity: 1 });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    alert('Đã thêm sản phẩm vào giỏ hàng!');
}

function displayCart() {
    const cartItems = document.getElementById('cartItems');
    const totalPrice = document.getElementById('totalPrice');

    if (!cartItems || !totalPrice) return;

    if (cart.length === 0) {
        cartItems.innerHTML = '<p style="text-align: center; padding: 20px; color: #666;">Giỏ hàng trống</p>';
        totalPrice.textContent = '0đ';
        return;
    }

    cartItems.innerHTML = '';
    let total = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';

        const imageHTML = item.image
            ? `<img src="${item.image}" alt="${item.name}" class="cart-item-image">`
            : `<div class="cart-item-image-placeholder">📦</div>`;

        cartItem.innerHTML = `
            ${imageHTML}
            <div class="cart-item-info">
                <h4>${item.name}</h4>
                <p class="cart-item-category">${item.categoryName || getCategoryName(item.category)}</p>
                <div class="cart-item-quantity">
                    <button class="cart-qty-btn" onclick="updateCartQuantity(${item.id}, -1)">-</button>
                    <span>${item.quantity}</span>
                    <button class="cart-qty-btn" onclick="updateCartQuantity(${item.id}, 1)">+</button>
                </div>
                <div class="cart-item-price">${formatPrice(itemTotal)}</div>
            </div>
            <button class="remove-btn" onclick="removeFromCart(${item.id})">🗑️</button>
        `;
        cartItems.appendChild(cartItem);
    });

    totalPrice.textContent = formatPrice(total);
}

function updateCartQuantity(productId, change) {
    const item = cart.find(item => item.id == productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(productId);
        } else {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            displayCart();
        }
    }
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id != productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    displayCart();
}

// ===== 6. SEARCH FUNCTIONALITY =====

function searchProducts() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    const searchTerm = searchInput.value.toLowerCase().trim();
    console.log('Searching for:', searchTerm);
    
    if (searchTerm === '') {
        // Empty search - quay về trang chủ
        isSearchMode = false;
        showHomePage();
        return;
    }
    
    console.log('=== ENTERING SEARCH MODE ===');
    isSearchMode = true;
    
    // Ẩn tất cả sections
    hideAllSectionsForSearch();
    
    // Hiển thị section kết quả tìm kiếm
    const productsSection = document.getElementById('productsSection');
    if (productsSection) {
        productsSection.style.display = 'block';
        productsSection.style.visibility = 'visible';
    }
    
    // Search in products
    const filteredProducts = products.filter(product =>
        product.name.toLowerCase().includes(searchTerm) ||
        (product.description && product.description.toLowerCase().includes(searchTerm)) ||
        (product.categoryName || getCategoryName(product.category)).toLowerCase().includes(searchTerm)
    );
    
    displaySearchResults(filteredProducts, searchTerm);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function displaySearchResults(productsToShow, searchTerm) {
    // GIẢI PHÁP CUỐI CÙNG: Tạo overlay hoàn toàn mới, không dùng bất kỳ element có sẵn nào
    console.log('=== NEW DISPLAY SEARCH RESULTS CALLED ===');
    console.log('Products:', productsToShow.length, 'Search term:', searchTerm);
    
    // Xóa overlay cũ nếu có
    const oldOverlay = document.getElementById('finalSearchOverlay');
    if (oldOverlay) {
        oldOverlay.remove();
        console.log('Removed old overlay');
    }
    
    // Tạo overlay mới hoàn toàn
    const overlay = document.createElement('div');
    overlay.id = 'finalSearchOverlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: white;
        z-index: 9999;
        overflow-y: auto;
        padding: 120px 20px 20px 20px;
    `;
    
    if (productsToShow.length === 0) {
        overlay.innerHTML = `
            <div style="text-align: center; color: #666;">
                <h3>Không tìm thấy sản phẩm</h3>
                <p>Không có sản phẩm nào phù hợp với từ khóa "<strong>${searchTerm}</strong>"</p>
                <button onclick="document.getElementById('finalSearchOverlay').remove(); showHomePage(); document.getElementById('searchInput').value='';" style="margin-top: 20px; padding: 10px 20px; background: #ff69b4; color: white; border: none; border-radius: 5px; cursor: pointer;">Quay về trang chủ</button>
            </div>
        `;
    } else {
        // Tạo nội dung
        let content = `<div style="max-width: 1200px; margin: 0 auto;">`;
        
        // Chỉ text đơn giản - TO ĐẬM HƠN NỮA
        content += `<h1 style="text-align: center; margin: 0 0 30px 0; color: #ff69b4; font-size: 36px; font-weight: 900; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">Tìm thấy ${productsToShow.length} sản phẩm cho "${searchTerm}"</h1>`;
        
        // Grid sản phẩm
        content += `<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px;">`;
        
        productsToShow.forEach(product => {
            content += `
                <div class="product-card" style="cursor: pointer;" onclick="showProductModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name}">
                    </div>
                    <h3>${product.name}</h3>
                    <p>Danh mục: ${getCategoryName(product.category)}</p>
                    <div class="product-price">${formatPrice(product.price)}</div>
                    <button class="add-to-cart" onclick="event.stopPropagation(); addToCart(${product.id})">Thêm vào giỏ</button>
                </div>
            `;
        });
        
        content += `</div></div>`;
        overlay.innerHTML = content;
    }
    
    // Thêm nút đóng
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '✕ Đóng';
    closeBtn.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #ff69b4;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        z-index: 10000;
    `;
    closeBtn.onclick = function() {
        overlay.remove();
        showHomePage();
        document.getElementById('searchInput').value = '';
    };
    overlay.appendChild(closeBtn);
    
    // Thêm vào body
    document.body.appendChild(overlay);
}

function hideAllSectionsForSearch() {
    const heroSection = document.querySelector('.hero-section');
    const allProductsPage = document.getElementById('allProductsPage');
    
    if (heroSection) heroSection.style.display = 'none';
    if (allProductsPage) allProductsPage.style.display = 'none';
}

// ===== 7. NAVIGATION FUNCTIONALITY =====

function showHomePage() {
    console.log('Showing home page...');
    isSearchMode = false;
    
    const heroSection = document.querySelector('.hero-section');
    const featuredProducts = document.getElementById('featuredProducts');
    const allProductsPage = document.getElementById('allProductsPage');
    const productsSection = document.getElementById('productsSection');
    
    // Hiển thị trang chủ (chỉ hero section)
    if (heroSection) heroSection.style.display = 'grid';
    
    // Ẩn tất cả các trang khác
    if (featuredProducts) featuredProducts.style.display = 'none';
    if (allProductsPage) allProductsPage.style.display = 'none';
    if (productsSection) productsSection.style.display = 'none';
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showAllProductsPage() {
    console.log('Showing all products page...');
    
    const heroSection = document.querySelector('.hero-section');
    const featuredProducts = document.getElementById('featuredProducts');
    const allProductsPage = document.getElementById('allProductsPage');
    const productsSection = document.getElementById('productsSection');
    
    // Ẩn trang chủ
    if (heroSection) heroSection.style.display = 'none';
    if (featuredProducts) featuredProducts.style.display = 'none';
    if (productsSection) productsSection.style.display = 'none';
    
    // Hiển thị trang sản phẩm
    if (allProductsPage) {
        allProductsPage.style.display = 'grid';
        
        // Hiển thị sản phẩm nếu đã load
        if (products && products.length > 0) {
            displayProducts(products, 'allProductGrid');
            
            // Cập nhật số lượng sản phẩm
            const productCount = document.getElementById('productCount');
            if (productCount) {
                productCount.textContent = products.length;
            }
        }
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showFeaturedProducts() {
    console.log('Showing featured products...');
    
    // Ẩn tất cả sections khác
    const heroSection = document.querySelector('.hero-section');
    const allProductsPage = document.getElementById('allProductsPage');
    const productsSection = document.getElementById('productsSection');
    
    if (heroSection) heroSection.style.display = 'none';
    if (allProductsPage) allProductsPage.style.display = 'none';
    if (productsSection) productsSection.style.display = 'none';
    
    // Hiển thị section sản phẩm nổi bật
    const featuredProducts = document.getElementById('featuredProducts');
    if (featuredProducts) {
        featuredProducts.style.display = 'block';
        
        // Load và hiển thị sản phẩm nổi bật (8 sản phẩm đầu tiên)
        if (products && products.length > 0) {
            displayProducts(products.slice(0, 8), 'featuredProductGrid');
        }
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ===== 8. EVENT LISTENERS =====

document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing Fresh Beauty...');
    
    // Load products từ API
    loadProductsFromAPI();
    
    // Khởi tạo slideshow
    showSlides(slideIndex);
    autoSlide();
    
    // Cập nhật số lượng giỏ hàng
    updateCartCount();
    
    // Search functionality
    const searchBtn = document.getElementById('searchBtn');
    const searchInput = document.getElementById('searchInput');
    
    if (searchBtn) {
        searchBtn.addEventListener('click', searchProducts);
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchProducts();
            }
        });
    }
    
    // Navigation
    const homeLink = document.getElementById('homeLink');
    const productsLink = document.getElementById('productsLink');
    
    if (homeLink) {
        homeLink.addEventListener('click', function(e) {
            e.preventDefault();
            showHomePage();
        });
    }
    
    if (productsLink) {
        productsLink.addEventListener('click', function(e) {
            e.preventDefault();
            showAllProductsPage();
        });
    }
    
    // Cart functionality
    const cartMenuLink = document.getElementById('cartMenuLink');
    if (cartMenuLink) {
        cartMenuLink.addEventListener('click', function(e) {
            e.preventDefault();
            displayCart();
            openModal('cartModal');
        });
    }
    
    // Close modal buttons
    const closeButtons = document.getElementsByClassName('close');
    Array.from(closeButtons).forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    });
    
    // Click outside modal to close
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    // ===== CHECKOUT FUNCTIONALITY =====
    
    // Debug: Log khi main.js được load
    console.log('=== CHECKOUT FUNCTIONALITY INITIALIZED ===');
    console.log('Cart variable:', typeof cart, cart);
    
    // Checkout button - Sử dụng event delegation vì nút có thể được tạo động
    document.addEventListener('click', function(e) {
        console.log('Document click detected:', e.target.className);
        
        if (e.target.classList.contains('checkout-btn')) {
            e.preventDefault();
            console.log('=== CHECKOUT BUTTON CLICKED ===');
            console.log('Current cart:', cart);
            
            if (cart.length === 0) {
                alert('Giỏ hàng trống!');
                return;
            }
            
            // Calculate total and show checkout modal
            const total = cart.reduce(function(sum, item) {
                return sum + (item.price * item.quantity);
            }, 0);
            
            console.log('Cart total:', total);
            
            const checkoutTotal = document.getElementById('checkoutTotal');
            console.log('Checkout total element:', checkoutTotal);
            
            if (checkoutTotal) {
                checkoutTotal.textContent = formatPrice(total);
            }
            
            const checkoutModal = document.getElementById('checkoutModal');
            console.log('Checkout modal element:', checkoutModal);
            
            if (checkoutModal) {
                console.log('Setting checkout modal display to block');
                checkoutModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
            
            const cartModal = document.getElementById('cartModal');
            if (cartModal) {
                console.log('Hiding cart modal');
                cartModal.style.display = 'none';
            }
        }
    });
    
    // Close checkout modal
    const closeCheckoutModal = document.getElementById('closeCheckoutModal');
    if (closeCheckoutModal) {
        closeCheckoutModal.addEventListener('click', function() {
            closeModal('checkoutModal');
        });
    }
    
    // Checkout form submit
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('=== CHECKOUT FORM SUBMITTED ===');
            
            const name = document.getElementById('checkoutName').value.trim();
            const phone = document.getElementById('checkoutPhone').value.trim();
            const address = document.getElementById('checkoutAddress').value.trim();
            
            // Validation
            if (!name || !phone || !address) {
                alert('Vui lòng điền đầy đủ thông tin!');
                return;
            }
            
            // Validate phone number
            if (!/^[0-9]{10,11}$/.test(phone)) {
                alert('Số điện thoại không hợp lệ! Vui lòng nhập 10-11 số.');
                return;
            }
            
            // Prepare order data
            const orderData = {
                customer_name: name,
                customer_phone: phone,
                customer_address: address,
                items: cart,
                userId: null // Khách vãng lai
            };
            
            console.log('Sending order data:', orderData);
            
            // Send to server
            fetch('api/save_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            })
            .then(function(response) {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(function(data) {
                console.log('Response data:', data);
                if (data.success) {
                    alert('Đặt hàng thành công! Cảm ơn bạn đã mua hàng.\nMã đơn hàng: #' + data.orderId + '\nChúng tôi sẽ liên hệ với bạn sớm nhất.');
                    cart = [];
                    localStorage.setItem('cart', JSON.stringify(cart));
                    updateCartCount();
                    closeModal('checkoutModal');
                    checkoutForm.reset();
                } else {
                    alert('Có lỗi xảy ra: ' + (data.message || 'Vui lòng thử lại'));
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại!');
            });
        });
    } else {
        console.error('ERROR: Checkout form not found!');
    }
    
    // Filter functionality
    const filterLinks = document.querySelectorAll('.filter-link');
    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active từ tất cả
            filterLinks.forEach(l => l.classList.remove('active'));
            
            // Add active cho link được click
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            if (filter === 'all') {
                displayProducts(products, 'allProductGrid');
            } else {
                const filtered = products.filter(p => p.category === filter);
                displayProducts(filtered, 'allProductGrid');
            }
        });
    });
    
    console.log('Fresh Beauty initialization complete');
});

// ===== 9. FOOTER FUNCTIONS =====

function filterProducts(category) {
    console.log('Filtering products by category:', category);
    
    // Hiển thị trang tất cả sản phẩm
    showAllProductsPage();
    
    // Filter sản phẩm theo danh mục
    setTimeout(() => {
        const filtered = products.filter(p => p.category === category);
        displayProducts(filtered, 'allProductGrid');
        
        // Cập nhật active filter
        const filterLinks = document.querySelectorAll('.filter-link');
        filterLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-filter') === category) {
                link.classList.add('active');
            }
        });
    }, 100);
}

function showContactModal() {
    alert('Liên hệ với chúng tôi qua số điện thoại: 017 568 4360\nGiờ làm việc: 8:00 – 22:00 (Thứ 2 – CN)\nChúng tôi sẽ sớm liên hệ với bạn');
}

function showPolicyModal() {
    alert('Chính sách đổi trả:\n\n• Đổi trả trong vòng 7 ngày\n• Sản phẩm còn nguyên seal, chưa sử dụng\n• Có hóa đơn mua hàng\n• Miễn phí đổi trả tại cửa hàng\n• Hoàn tiền 100% nếu lỗi từ nhà sản xuất');
}

function showShippingModal() {
    alert('Chính sách giao hàng:\n\n• Giao hàng miễn phí đơn từ 500.000đ\n• Giao hàng trong 1-2 ngày tại TP. Trà Vinh\n• Giao hàng toàn quốc 2-5 ngày\n• Kiểm tra hàng trước khi thanh toán\n• Hỗ trợ đổi trả tại nhà');
}

function showPrivacyModal() {
    alert('Bảo mật thông tin:\n\n• Thông tin khách hàng được bảo mật tuyệt đối\n• Không chia sẻ thông tin cho bên thứ 3\n• Thanh toán an toàn, bảo mật\n• Tuân thủ luật bảo vệ dữ liệu cá nhân\n• Có thể yêu cầu xóa thông tin bất kỳ lúc nào');
}

// ===== 10. GLOBAL FUNCTIONS FOR COMPATIBILITY =====

// Các function này cần thiết cho compatibility với HTML onclick events
window.addToCart = addToCart;
window.updateCartQuantity = updateCartQuantity;
window.removeFromCart = removeFromCart;
window.showHomePage = showHomePage;
window.showAllProductsPage = showAllProductsPage;
window.showFeaturedProducts = showFeaturedProducts;
window.changeSlide = changeSlide;
window.currentSlide = currentSlide;
window.openModal = openModal;
window.closeModal = closeModal;
window.filterProducts = filterProducts;
window.showContactModal = showContactModal;
window.showPolicyModal = showPolicyModal;
window.showShippingModal = showShippingModal;
window.showPrivacyModal = showPrivacyModal;

// ===================================================================
// KẾT THÚC FILE JAVASCRIPT TỔNG HỢP
// Hướng dẫn sử dụng:
// 1. File này thay thế tất cả các file JS riêng lẻ
// 2. Chỉ cần include file này trong HTML
// 3. Tất cả chức năng đã được tối ưu và gom gọn
// ===================================================================