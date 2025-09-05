class ModernNavbar {
    constructor() {
        this.init();
    }

    init() {
        // Check if navbar already exists to prevent duplicates
        if (document.querySelector('.modern-header')) {
            return;
        }
        
        this.createNavbar();
        this.handleSession();
        this.setupMobileMenu();
        // Search removed per requirements
        this.setupScrollEffects();
        this.updateCartCount();
    }

    createNavbar() {
        const navbarHTML = `
            <header class="modern-header">
                <div class="nav-container">
                    <!-- Logo Section -->
                    <div class="logo-section">
                        <a href="medico.html" class="logo-link">
                            <img src="./Images/MEDI_MERGE_LOGO.png" alt="MediMerge" class="logo-img">
                            <span class="logo-text">Medi<span class="logo-accent">Merge</span></span>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="desktop-nav">
                        <a href="medico.html" class="nav-link"><i class="fas fa-home"></i> Home</a>
                        <a href="product.html" class="nav-link"><i class="fas fa-pills"></i> Products</a>
                        <a href="medico.html#features" class="nav-link"><i class="fas fa-info-circle"></i> About</a>
                        <a href="medico.html#contact" class="nav-link"><i class="fas fa-envelope"></i> Contact</a>
                    </nav>

                    <!-- Search removed -->

                    <!-- User Actions -->
                    <div class="user-actions">
                        <a href="cart.html" class="cart-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count" id="cart-count">0</span>
                        </a>
                        <div class="user-dropdown">
                            <a href="login.php" id="sign-in-link" class="auth-link">Sign In</a>
                            <div id="user-menu" class="user-menu" style="display:none">
                                <button class="user-icon-btn" id="user-icon-btn">
                                    <i class="fas fa-user-circle"></i>
                                </button>
                                <div class="user-dropdown-content" id="user-dropdown-content">
                                    <div class="user-info">
                                        <span id="username-display" class="username-display"></span>
                                    </div>
                                    <a href="user_dashboard.html" class="dropdown-link">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                    <a href="logout.php" class="dropdown-link logout-link">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button class="mobile-menu-btn" id="mobile-menu-btn">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>

                <!-- Mobile Navigation -->
                <nav class="mobile-nav" id="mobile-nav">
                    <a href="medico.html" class="mobile-nav-link"><i class="fas fa-home"></i> Home</a>
                    <a href="product.html" class="mobile-nav-link"><i class="fas fa-pills"></i> Products</a>
                    <a href="medico.html#features" class="mobile-nav-link"><i class="fas fa-info-circle"></i> About</a>
                    <a href="contact.html" class="mobile-nav-link"><i class="fas fa-envelope"></i> Contact</a>
                    <a href="cart.html" class="mobile-nav-link"><i class="fas fa-shopping-cart"></i> Cart</a>
                    <a href="user_dashboard.html" id="mobile-dashboard-link" class="mobile-nav-link" style="display:none"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="admin/admin_dashboard.php" id="mobile-admin-link" class="mobile-nav-link" style="display:none"><i class="fas fa-cog"></i> Admin</a>
                    <a href="login.php" id="mobile-sign-in" class="mobile-nav-link"><i class="fas fa-sign-in-alt"></i> Sign In</a>
                    <a href="logout.php" id="mobile-logout" class="mobile-nav-link" style="display:none"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </header>
        `;

        // Insert navbar at the beginning of body
        document.body.insertAdjacentHTML('afterbegin', navbarHTML);
        
        // Expose instance for cart count updates
        window.navbar = this;
        
        this.addNavbarStyles();
    }

    addNavbarStyles() {
        const styles = `
            <style>
                .modern-header {
                    background: rgba(255, 255, 255, 0.98);
                    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                    position: sticky;
                    top: 0;
                    z-index: 1000;
                    backdrop-filter: blur(20px);
                    border-bottom: 1px solid rgba(17, 182, 113, 0.1);
                    transition: all 0.3s ease;
                    width: 100%;
                }

                .modern-header.scrolled {
                    background: rgba(255, 255, 255, 0.95);
                    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                }

                .nav-container {
                    max-width: 1400px;
                    width: 100%;
                    margin: 0 auto;
                    padding: 0 clamp(15px, 3vw, 30px);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    height: clamp(65px, 10vw, 85px);
                    gap: clamp(10px, 3vw, 25px);
                    position: relative;
                    box-sizing: border-box;
                }

                .logo-section {
                    display: flex;
                    align-items: center;
                    flex-shrink: 0;
                    min-width: fit-content;
                }

                .logo-link {
                    display: flex;
                    align-items: center;
                    text-decoration: none;
                    color: inherit;
                    transition: all 0.3s ease;
                    padding: 4px 8px;
                    border-radius: 8px;
                }

                .logo-link:hover {
                    transform: scale(1.02);
                    background: rgba(17, 182, 113, 0.05);
                }

                .logo-img {
                    width: clamp(32px, 6vw, 48px);
                    height: clamp(32px, 6vw, 48px);
                    margin-right: clamp(6px, 2vw, 15px);
                    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
                }

                .logo-text {
                    font-size: clamp(16px, 4vw, 28px);
                    font-weight: 800;
                    color: #1a1a1a;
                    letter-spacing: -0.5px;
                    white-space: nowrap;
                }

                .logo-accent {
                    color: #11b671;
                    text-shadow: 0 2px 4px rgba(17, 182, 113, 0.2);
                }

                .desktop-nav {
                    display: flex;
                    flex-wrap: wrap;
                    gap: clamp(8px, 2vw, 20px);
                    margin: 0 clamp(5px, 2vw, 15px);
                }

                .nav-link {
                    color: #333;
                    text-decoration: none;
                    font-weight: 600;
                    font-size: clamp(13px, 1.5vw, 16px);
                    transition: all 0.3s ease;
                    position: relative;
                    padding: clamp(6px, 1.5vw, 12px) clamp(8px, 2vw, 18px);
                    border-radius: 20px;
                    white-space: nowrap;
                    letter-spacing: 0.2px;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                }

                .nav-link:hover {
                    color: #11b671;
                    background: rgba(17, 182, 113, 0.1);
                    transform: translateY(-1px);
                }

                .nav-link::after {
                    content: '';
                    position: absolute;
                    bottom: 4px;
                    left: 50%;
                    width: 0;
                    height: 2px;
                    background: #11b671;
                    transition: all 0.3s ease;
                    transform: translateX(-50%);
                }

                .nav-link:hover::after {
                    width: 60%;
                }

                /* Search removed */

                .user-actions {
                    display: flex;
                    align-items: center;
                    gap: clamp(4px, 1vw, 8px);
                    flex-shrink: 0;
                    min-width: fit-content;
                }

                .cart-link {
                    position: relative;
                    color: #333;
                    font-size: clamp(16px, 3vw, 20px);
                    text-decoration: none;
                    transition: all 0.3s ease;
                    padding: clamp(6px, 1.5vw, 10px);
                    border-radius: 50%;
                    flex-shrink: 0;
                }

                .cart-link:hover {
                    color: #11b671;
                    background: rgba(17, 182, 113, 0.1);
                    transform: scale(1.05);
                }

                .cart-count {
                    position: absolute;
                    top: 0;
                    right: 0;
                    background: linear-gradient(135deg, #ff4757, #ff3742);
                    color: white;
                    border-radius: 50%;
                    width: clamp(16px, 3vw, 20px);
                    height: clamp(16px, 3vw, 20px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: clamp(10px, 1.5vw, 12px);
                    font-weight: bold;
                    box-shadow: 0 2px 8px rgba(255, 71, 87, 0.3);
                    animation: pulse 2s infinite;
                }

                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                    100% { transform: scale(1); }
                }

                .user-dropdown {
                    position: relative;
                    display: flex;
                    align-items: center;
                }

                .auth-link {
                    color: #333;
                    text-decoration: none;
                    font-weight: 600;
                    padding: clamp(4px, 1vw, 8px) clamp(6px, 1.5vw, 12px);
                    border-radius: 18px;
                    transition: all 0.3s ease;
                    border: 1px solid transparent;
                    font-size: clamp(11px, 1.3vw, 14px);
                    white-space: nowrap;
                    text-align: center;
                    min-width: fit-content;
                }

                .auth-link:hover {
                    background: rgba(17, 182, 113, 0.1);
                    border-color: rgba(17, 182, 113, 0.3);
                    transform: translateY(-1px);
                }

                .user-icon-btn {
                    background: none;
                    border: none;
                    color: #11b671;
                    font-size: clamp(18px, 3vw, 24px);
                    cursor: pointer;
                    padding: clamp(4px, 1vw, 8px);
                    border-radius: 50%;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .user-icon-btn:hover {
                    background: rgba(17, 182, 113, 0.1);
                    transform: scale(1.05);
                }

                .user-dropdown-content {
                    position: absolute;
                    top: 100%;
                    right: 0;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
                    border: 1px solid rgba(17, 182, 113, 0.1);
                    min-width: 200px;
                    padding: 8px 0;
                    opacity: 0;
                    visibility: hidden;
                    transform: translateY(-10px);
                    transition: all 0.3s ease;
                    z-index: 1000;
                }

                .user-dropdown-content.show {
                    opacity: 1;
                    visibility: visible;
                    transform: translateY(0);
                }

                .user-info {
                    padding: 12px 16px;
                    border-bottom: 1px solid #f0f0f0;
                    background: rgba(17, 182, 113, 0.05);
                }

                .username-display {
                    font-weight: 600;
                    color: #11b671;
                    font-size: 14px;
                }

                .dropdown-link {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 12px 16px;
                    color: #333;
                    text-decoration: none;
                    font-size: 14px;
                    transition: all 0.3s ease;
                }

                .dropdown-link:hover {
                    background: rgba(17, 182, 113, 0.1);
                    color: #11b671;
                }

                .dropdown-link.logout-link:hover {
                    background: rgba(239, 68, 68, 0.1);
                    color: #ef4444;
                }

                .logout-link {
                    background: linear-gradient(135deg, #ff4757, #ff3742);
                    color: white !important;
                    border-color: #ff4757;
                }

                .logout-link:hover {
                    background: linear-gradient(135deg, #ff3742, #e63946);
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(255, 71, 87, 0.3);
                }

                .username-display {
                    color: #11b671;
                    font-weight: 700;
                    font-size: clamp(10px, 1.2vw, 13px);
                    display: none;
                    background: rgba(17, 182, 113, 0.1);
                    padding: clamp(4px, 1vw, 6px) clamp(6px, 1.5vw, 10px);
                    border-radius: 15px;
                    border: 1px solid rgba(17, 182, 113, 0.2);
                    white-space: nowrap;
                    max-width: clamp(60px, 15vw, 120px);
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                /* Toast popups */
                .mm-toast {
                    position: fixed;
                    right: 20px;
                    top: 20px;
                    z-index: 2000;
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                }
                
                .mm-toast .item {
                    background: #333;
                    color: #fff;
                    padding: 12px 16px;
                    border-radius: 10px;
                    box-shadow: 0 5px 15px rgba(0,0,0,.2);
                    opacity: .95;
                    transform: translateX(100%);
                    transition: transform .3s ease;
                    max-width: 300px;
                    word-wrap: break-word;
                }
                
                .mm-toast .item.show {
                    transform: translateX(0);
                }

                .mobile-menu-btn {
                    display: none;
                    flex-direction: column;
                    background: none;
                    border: none;
                    cursor: pointer;
                    padding: clamp(6px, 1.5vw, 8px);
                    border-radius: 6px;
                    transition: all 0.3s ease;
                    flex-shrink: 0;
                }

                .mobile-menu-btn:hover {
                    background: rgba(17, 182, 113, 0.1);
                }

                .mobile-menu-btn span {
                    width: clamp(20px, 4vw, 26px);
                    height: 2.5px;
                    background: #333;
                    margin: clamp(2px, 0.5vw, 3px) 0;
                    transition: 0.3s;
                    border-radius: 2px;
                }

                .mobile-nav {
                    display: none;
                    background: rgba(255, 255, 255, 0.98);
                    backdrop-filter: blur(20px);
                    padding: 20px;
                    border-top: 1px solid rgba(17, 182, 113, 0.1);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
                    animation: slideDown 0.3s ease;
                    position: absolute;
                    top: 100%;
                    left: 0;
                    right: 0;
                    width: 100%;
                    max-height: 80vh;
                    overflow-y: auto;
                    z-index: 1000;
                }

                @keyframes slideDown {
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                .mobile-nav-link {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    color: #333;
                    text-decoration: none;
                    padding: 15px 0;
                    border-bottom: 1px solid rgba(0,0,0,0.08);
                    font-weight: 600;
                    transition: all 0.3s ease;
                    font-size: clamp(15px, 3vw, 17px);
                    letter-spacing: 0.2px;
                }

                .mobile-nav-link:hover {
                    color: #11b671;
                    padding-left: 15px;
                    background: rgba(17, 182, 113, 0.05);
                    border-radius: 8px;
                    transform: translateX(5px);
                }

                .mobile-nav-link:last-child {
                    border-bottom: none;
                }

                /* Responsive Design */
                @media (max-width: 1200px) {
                    .nav-container {
                        gap: 20px;
                    }
                    
                    .desktop-nav {
                        gap: 15px;
                        margin: 0 10px;
                    }
                    
                    /* Search removed */

                    .auth-section {
                        gap: 8px;
                    }
                }

                @media (max-width: 1024px) {
                    .nav-container {
                        gap: 15px;
                    }
                    
                    .desktop-nav {
                        gap: 12px;
                        margin: 0 8px;
                    }
                    
                    /* Search removed */

                    .user-actions {
                        gap: 10px;
                    }

                    .auth-section {
                        gap: 6px;
                    }

                    .nav-link {
                        padding: 8px 12px;
                        font-size: 14px;
                    }

                    .auth-link {
                        padding: 6px 10px;
                        font-size: 12px;
                    }
                }

                @media (max-width: 900px) {
                    /* Search removed */

                    .desktop-nav {
                        gap: 8px;
                    }

                    .nav-link {
                        padding: 6px 10px;
                        font-size: 13px;
                    }

                    .auth-link {
                        padding: 5px 8px;
                        font-size: 11px;
                    }

                    .username-display {
                        max-width: 80px;
                        font-size: 10px;
                    }
                }

                @media (max-width: 768px) {
                    .desktop-nav {
                        display: none;
                    }

                    .mobile-menu-btn {
                        display: flex;
                    }

                    .nav-container {
                        gap: 12px;
                        height: 65px;
                    }

                    .user-actions {
                        gap: 8px;
                    }

                    .auth-section {
                        gap: 6px;
                    }

                    .auth-link {
                        padding: 5px 8px;
                        font-size: 11px;
                    }

                    .username-display {
                        max-width: 70px;
                        font-size: 10px;
                        padding: 3px 6px;
                    }
                }

                @media (max-width: 640px) {
                    .nav-container {
                        gap: 10px;
                        height: 60px;
                    }

                    .user-actions {
                        gap: 6px;
                    }

                    .auth-section {
                        gap: 4px;
                    }

                    .auth-link {
                        padding: 4px 6px;
                        font-size: 10px;
                    }

                    .username-display {
                        max-width: 60px;
                        font-size: 9px;
                        padding: 2px 4px;
                    }

                    .cart-link {
                        padding: 6px;
                        font-size: 16px;
                    }
                }

                @media (max-width: 480px) {
                    .nav-container {
                        padding: 0 10px;
                        gap: 8px;
                        height: 55px;
                    }

                    .user-actions {
                        gap: 4px;
                    }

                    .auth-section {
                        gap: 3px;
                    }

                    .auth-link {
                        padding: 3px 5px;
                        font-size: 9px;
                        border-radius: 12px;
                    }

                    .username-display {
                        max-width: 50px;
                        font-size: 8px;
                        padding: 2px 3px;
                    }

                    .cart-link {
                        padding: 4px;
                        font-size: 14px;
                    }

                    .logo-text {
                        font-size: 14px;
                    }

                    .logo-img {
                        width: 28px;
                        height: 28px;
                        margin-right: 6px;
                    }
                }

                @media (max-width: 380px) {
                    .nav-container {
                        padding: 0 8px;
                        gap: 6px;
                    }

                    .auth-link {
                        padding: 2px 4px;
                        font-size: 8px;
                        min-width: auto;
                    }

                    .username-display {
                        max-width: 45px;
                        font-size: 7px;
                    }

                    .logo-text {
                        font-size: 12px;
                    }

                    .logo-img {
                        width: 24px;
                        height: 24px;
                        margin-right: 4px;
                    }
                }

                /* Utility class for hiding overflow */
                .modern-header {
                    overflow-x: hidden;
                    width: 100%;
                }

                /* Ensure no horizontal scroll */
                .nav-container {
                    min-width: 0;
                    overflow: hidden;
                }

                .user-actions {
                    min-width: 0;
                    overflow: hidden;
                }

                .auth-section {
                    min-width: 0;
                    overflow: hidden;
                }
            </style>
        `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    handleSession() {
        // Check if user is logged in
        fetch('./session.php')
            .then(response => response.json())
            .then(data => {
                if (data.username) {
                    // User is logged in
                    document.getElementById('sign-in-link').style.display = 'none';
                    document.getElementById('mobile-sign-in').style.display = 'none';
                    document.getElementById('user-menu').style.display = 'block';
                    document.getElementById('mobile-logout').style.display = 'block';
                    document.getElementById('mobile-dashboard-link').style.display = 'block';
                    
                    // Show admin link if user is admin
                    if (data.isAdmin) {
                        document.getElementById('mobile-admin-link').style.display = 'block';
                    }
                    
                    document.getElementById('username-display').textContent = data.username;
                    
                    // Setup dropdown functionality
                    this.setupUserDropdown();
                } else {
                    // User is not logged in
                    document.getElementById('sign-in-link').style.display = 'inline-block';
                    document.getElementById('mobile-sign-in').style.display = 'block';
                    document.getElementById('user-menu').style.display = 'none';
                    document.getElementById('mobile-logout').style.display = 'none';
                    document.getElementById('mobile-dashboard-link').style.display = 'none';
                    document.getElementById('mobile-admin-link').style.display = 'none';
                }
            })
            .catch(error => {
                console.log('Session check failed:', error);
            });
    }

    setupUserDropdown() {
        const userIconBtn = document.getElementById('user-icon-btn');
        const dropdownContent = document.getElementById('user-dropdown-content');
        
        if (userIconBtn && dropdownContent) {
            userIconBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdownContent.classList.toggle('show');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userIconBtn.contains(e.target) && !dropdownContent.contains(e.target)) {
                    dropdownContent.classList.remove('show');
                }
            });
        }
    }

    setupMobileMenu() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileNav = document.getElementById('mobile-nav');
        let isOpen = false;

        mobileMenuBtn.addEventListener('click', () => {
            isOpen = !isOpen;
            
            if (isOpen) {
                mobileNav.style.display = 'block';
                mobileMenuBtn.classList.add('active');
                
                // Animate hamburger to X
                mobileMenuBtn.querySelectorAll('span').forEach((span, index) => {
                    if (index === 0) span.style.transform = 'rotate(45deg) translate(5px, 5px)';
                    if (index === 1) span.style.opacity = '0';
                    if (index === 2) span.style.transform = 'rotate(-45deg) translate(7px, -6px)';
                });
            } else {
                mobileNav.style.display = 'none';
                mobileMenuBtn.classList.remove('active');
                
                // Reset hamburger
                mobileMenuBtn.querySelectorAll('span').forEach(span => {
                    span.style.transform = 'none';
                    span.style.opacity = '1';
                });
            }
        });

        // Close mobile menu when clicking on a link
        mobileNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileNav.style.display = 'none';
                mobileMenuBtn.classList.remove('active');
                isOpen = false;
                
                // Reset hamburger
                mobileMenuBtn.querySelectorAll('span').forEach(span => {
                    span.style.transform = 'none';
                    span.style.opacity = '1';
                });
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenuBtn.contains(e.target) && !mobileNav.contains(e.target)) {
                mobileNav.style.display = 'none';
                mobileMenuBtn.classList.remove('active');
                isOpen = false;
                
                // Reset hamburger
                mobileMenuBtn.querySelectorAll('span').forEach(span => {
                    span.style.transform = 'none';
                    span.style.opacity = '1';
                });
            }
        });
    }

    setupSearch() {
        const searchInput = document.querySelector('.search-input');
        const searchBtn = document.querySelector('.search-btn');

        searchBtn.addEventListener('click', () => {
            const query = searchInput.value.trim();
            if (query) {
                window.location.href = `product.html?search=${encodeURIComponent(query)}`;
            }
        });

        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const query = searchInput.value.trim();
                if (query) {
                    window.location.href = `product.html?search=${encodeURIComponent(query)}`;
                }
            }
        });
    }

    setupScrollEffects() {
        const header = document.querySelector('.modern-header');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    updateCartCount(count = 0) {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = count;
            cartCount.style.display = count > 0 ? 'flex' : 'none';
        }
    }

    showToast(message, duration = 3000) {
        const toast = document.createElement('div');
        toast.className = 'mm-toast';
        toast.innerHTML = `<div class="item">${message}</div>`;
        
        document.body.appendChild(toast);
        
        const item = toast.querySelector('.item');
        setTimeout(() => item.classList.add('show'), 100);
        
        setTimeout(() => {
            item.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, duration);
    }
}

// Initialize navbar when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (!document.querySelector('.modern-header')) {
        const nav = new ModernNavbar();
        // Fetch initial cart count
        fetch('./api/cart_api.php')
            .then(r => r.json())
            .then(d => {
                if (d && d.success && typeof d.item_count !== 'undefined') {
                    nav.updateCartCount(d.item_count);
                }
            })
            .catch(() => {});
    }
});