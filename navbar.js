// Modern Responsive Navbar Component
class ModernNavbar {
    constructor() {
        this.init();
    }

    init() {
        this.createNavbar();
        this.handleSession();
        this.setupMobileMenu();
        this.setupSearch();
        this.setupScrollEffects();
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
                        <a href="medico.html" class="nav-link">Home</a>
                        <a href="product.html" class="nav-link">Products</a>
                        <a href="medico.html#features" class="nav-link">About</a>
                        <a href="medico.html#contact" class="nav-link">Contact</a>
                    </nav>

                    <!-- Search Bar -->
                    <div class="search-section">
                        <div class="search-container">
                            <input type="text" placeholder="Search products..." class="search-input">
                            <button class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- User Actions -->
                    <div class="user-actions">
                        <a href="cart.html" class="cart-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count" id="cart-count">0</span>
                        </a>
                        <div class="auth-section">
                            <a href="user_dashboard.html" id="dashboard-link" class="auth-link" style="display:none">Dashboard</a>
                            <a href="login.php" id="sign-in-link" class="auth-link">Sign In</a>
                            <span id="username-display" class="username-display"></span>
                            <a href="logout.php" id="logout-link" class="auth-link logout-link">Logout</a>
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
                    <a href="medico.html" class="mobile-nav-link">Home</a>
                    <a href="product.html" class="mobile-nav-link">Products</a>
                    <a href="medico.html#features" class="mobile-nav-link">About</a>
                    <a href="medico.html#contact" class="mobile-nav-link">Contact</a>
                    <a href="cart.html" class="mobile-nav-link">Cart</a>
                    <a href="user_dashboard.html" id="mobile-dashboard-link" class="mobile-nav-link" style="display:none">Dashboard</a>
                    <a href="login.php" id="mobile-sign-in" class="mobile-nav-link">Sign In</a>
                    <a href="logout.php" id="mobile-logout" class="mobile-nav-link">Logout</a>
                </nav>
            </header>
        `;

        // Insert navbar at the beginning of body
        document.body.insertAdjacentHTML('afterbegin', navbarHTML);
        // expose instance for cart count updates
        window.navbar = this;
        this.addNavbarStyles();
    }

    addNavbarStyles() {
        const styles = `
            <style>
                .modern-header {
                    background: rgba(255, 255, 255, 0.98);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
                    position: sticky;
                    top: 0;
                    z-index: 1000;
                    backdrop-filter: blur(20px);
                    border-bottom: 1px solid rgba(17, 182, 113, 0.1);
                    transition: all 0.3s ease;
                }

                .modern-header.scrolled {
                    background: rgba(255, 255, 255, 0.95);
                    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                }

                .nav-container {
                    max-width: 1400px;
                    margin: 0 auto;
                    padding: 0 20px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    height: 80px;
                }

                .logo-section {
                    display: flex;
                    align-items: center;
                    flex-shrink: 0;
                }

                .logo-link {
                    display: flex;
                    align-items: center;
                    text-decoration: none;
                    color: inherit;
                    transition: all 0.3s ease;
                }

                .logo-link:hover {
                    transform: scale(1.05);
                }

                .logo-img {
                    width: 45px;
                    height: 45px;
                    margin-right: 12px;
                    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
                }

                .logo-text {
                    font-size: clamp(20px, 4vw, 26px);
                    font-weight: 800;
                    color: #1a1a1a;
                    letter-spacing: -0.5px;
                }

                .logo-accent {
                    color: #11b671;
                    text-shadow: 0 2px 4px rgba(17, 182, 113, 0.2);
                }

                .desktop-nav {
                    display: flex;
                    gap: clamp(20px, 3vw, 35px);
                }

                .nav-link {
                    color: #333;
                    text-decoration: none;
                    font-weight: 600;
                    font-size: clamp(14px, 2vw, 16px);
                    transition: all 0.3s ease;
                    position: relative;
                    padding: 8px 16px;
                    border-radius: 20px;
                    white-space: nowrap;
                }

                .nav-link:hover {
                    color: #11b671;
                    background: rgba(17, 182, 113, 0.1);
                    transform: translateY(-2px);
                }

                .nav-link::after {
                    content: '';
                    position: absolute;
                    bottom: -2px;
                    left: 50%;
                    width: 0;
                    height: 3px;
                    background: linear-gradient(135deg, #11b671, #0ea55d);
                    transition: all 0.3s ease;
                    transform: translateX(-50%);
                    border-radius: 2px;
                }

                .nav-link:hover::after {
                    width: 80%;
                }

                .search-section {
                    flex: 1;
                    max-width: 450px;
                    margin: 0 30px;
                }

                .search-container {
                    position: relative;
                    display: flex;
                    align-items: center;
                }

                .search-input {
                    width: 100%;
                    padding: 14px 50px 14px 20px;
                    border: 2px solid #e8e8e8;
                    border-radius: 30px;
                    background: #f8f9fa;
                    font-size: 15px;
                    outline: none;
                    transition: all 0.3s ease;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                }

                .search-input:focus {
                    background: #ffffff;
                    border-color: #11b671;
                    box-shadow: 0 0 0 4px rgba(17, 182, 113, 0.1), 0 4px 16px rgba(0,0,0,0.1);
                }

                .search-btn {
                    position: absolute;
                    right: 6px;
                    background: linear-gradient(135deg, #11b671, #0ea55d);
                    border: none;
                    border-radius: 50%;
                    width: 38px;
                    height: 38px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    box-shadow: 0 2px 8px rgba(17, 182, 113, 0.3);
                }

                .search-btn:hover {
                    background: linear-gradient(135deg, #0ea55d, #0d9488);
                    transform: scale(1.1);
                    box-shadow: 0 4px 16px rgba(17, 182, 113, 0.4);
                }

                .user-actions {
                    display: flex;
                    align-items: center;
                    gap: clamp(15px, 2vw, 25px);
                    flex-shrink: 0;
                }

                .cart-link {
                    position: relative;
                    color: #333;
                    font-size: clamp(18px, 3vw, 22px);
                    text-decoration: none;
                    transition: all 0.3s ease;
                    padding: 10px;
                    border-radius: 50%;
                }

                .cart-link:hover {
                    color: #11b671;
                    background: rgba(17, 182, 113, 0.1);
                    transform: scale(1.1);
                }

                .cart-count {
                    position: absolute;
                    top: 2px;
                    right: 2px;
                    background: linear-gradient(135deg, #ff4757, #ff3742);
                    color: white;
                    border-radius: 50%;
                    width: 22px;
                    height: 22px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 12px;
                    font-weight: bold;
                    box-shadow: 0 2px 8px rgba(255, 71, 87, 0.3);
                    animation: pulse 2s infinite;
                }

                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.1); }
                    100% { transform: scale(1); }
                }

                .auth-section {
                    display: flex;
                    align-items: center;
                    gap: clamp(12px, 2vw, 18px);
                }

                .auth-link {
                    color: #333;
                    text-decoration: none;
                    font-weight: 600;
                    padding: clamp(8px, 2vw, 10px) clamp(12px, 3vw, 20px);
                    border-radius: 25px;
                    transition: all 0.3s ease;
                    border: 2px solid transparent;
                    font-size: clamp(12px, 2vw, 14px);
                    white-space: nowrap;
                }

                .auth-link:hover {
                    background: rgba(17, 182, 113, 0.1);
                    border-color: rgba(17, 182, 113, 0.3);
                    transform: translateY(-2px);
                }

                .logout-link {
                    background: linear-gradient(135deg, #ff4757, #ff3742);
                    color: white;
                    border-color: #ff4757;
                }

                .logout-link:hover {
                    background: linear-gradient(135deg, #ff3742, #e63946);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 16px rgba(255, 71, 87, 0.3);
                }

                /* Toast popups */
                .mm-toast{
                    position: fixed;
                    right: 20px;
                    top: 20px;
                    z-index: 2000;
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                }
                .mm-toast .item{
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
                .mm-toast .item.show{
                    transform: translateX(0);
                }

                .username-display {
                    color: #11b671;
                    font-weight: 700;
                    font-size: clamp(12px, 2vw, 14px);
                    display: none;
                    background: rgba(17, 182, 113, 0.1);
                    padding: clamp(6px, 1.5vw, 8px) clamp(12px, 2vw, 16px);
                    border-radius: 20px;
                    border: 1px solid rgba(17, 182, 113, 0.2);
                    white-space: nowrap;
                }

                .mobile-menu-btn {
                    display: none;
                    flex-direction: column;
                    background: none;
                    border: none;
                    cursor: pointer;
                    padding: 8px;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                }

                .mobile-menu-btn:hover {
                    background: rgba(17, 182, 113, 0.1);
                }

                .mobile-menu-btn span {
                    width: 28px;
                    height: 3px;
                    background: #333;
                    margin: 4px 0;
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
                }

                @keyframes slideDown {
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                .mobile-nav-link {
                    display: block;
                    color: #333;
                    text-decoration: none;
                    padding: 18px 0;
                    border-bottom: 1px solid rgba(0,0,0,0.1);
                    font-weight: 600;
                    transition: all 0.3s ease;
                    font-size: 16px;
                }

                .mobile-nav-link:hover {
                    color: #11b671;
                    padding-left: 15px;
                    background: rgba(17, 182, 113, 0.05);
                    border-radius: 8px;
                }

                .mobile-nav-link:last-child {
                    border-bottom: none;
                }

                /* Responsive Design */
                @media (max-width: 1200px) {
                    .nav-container {
                        padding: 0 15px;
                    }
                    
                    .desktop-nav {
                        gap: 20px;
                    }
                    
                    .search-section {
                        margin: 0 20px;
                        max-width: 300px;
                    }
                }

                @media (max-width: 1024px) {
                    .nav-container {
                        padding: 0 15px;
                    }
                    
                    .desktop-nav {
                        gap: 15px;
                    }
                    
                    .search-section {
                        margin: 0 15px;
                        max-width: 250px;
                    }

                    .user-actions {
                        gap: 15px;
                    }
                }

                @media (max-width: 768px) {
                    .desktop-nav, .search-section {
                        display: none;
                    }

                    .mobile-menu-btn {
                        display: flex;
                    }

                    .nav-container {
                        padding: 0 15px;
                        height: 70px;
                    }

                    .logo-text {
                        font-size: 20px;
                    }

                    .logo-img {
                        width: 40px;
                        height: 40px;
                    }

                    .user-actions {
                        gap: 12px;
                    }

                    .auth-link {
                        padding: 8px 14px;
                        font-size: 13px;
                    }

                    .cart-link {
                        font-size: 20px;
                    }
                }

                @media (max-width: 480px) {
                    .nav-container {
                        height: 65px;
                        padding: 0 12px;
                    }

                    .logo-img {
                        width: 35px;
                        height: 35px;
                    }

                    .logo-text {
                        font-size: 18px;
                    }

                    .cart-link {
                        font-size: 18px;
                    }

                    .auth-link {
                        padding: 6px 10px;
                        font-size: 12px;
                    }

                    .mobile-nav {
                        padding: 15px;
                    }

                    .user-actions {
                        gap: 8px;
                    }
                }

                @media (max-width: 360px) {
                    .nav-container {
                        padding: 0 8px;
                    }

                    .logo-img {
                        width: 30px;
                        height: 30px;
                        margin-right: 8px;
                    }

                    .logo-text {
                        font-size: 16px;
                    }

                    .auth-link {
                        padding: 5px 8px;
                        font-size: 11px;
                    }

                    .cart-link {
                        font-size: 16px;
                    }
                }
            </style>
        `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    handleSession() {
        fetch('session.php')
            .then(response => response.json())
            .then(data => {
                const usernameDisplay = document.getElementById("username-display");
                const signInLink = document.getElementById("sign-in-link");
                const logoutLink = document.getElementById("logout-link");
                const mobileSignIn = document.getElementById("mobile-sign-in");
                const mobileLogout = document.getElementById("mobile-logout");
                const dashboardLink = document.getElementById("dashboard-link");
                const mobileDashboardLink = document.getElementById("mobile-dashboard-link");

                if (data.username) {
                    usernameDisplay.textContent = `Hello, ${data.username}`;
                    usernameDisplay.style.display = "inline";
                    signInLink.style.display = "none";
                    logoutLink.style.display = "inline";
                    mobileSignIn.style.display = "none";
                    mobileLogout.style.display = "block";
                    
                    // Check if user is admin
                    if (data.username === 'TheAdmin') {
                        dashboardLink.style.display = "inline";
                        mobileDashboardLink.style.display = "block";
                    } else {
                        dashboardLink.style.display = "none";
                        mobileDashboardLink.style.display = "none";
                    }
                } else {
                    usernameDisplay.style.display = "none";
                    signInLink.style.display = "inline";
                    logoutLink.style.display = "none";
                    mobileSignIn.style.display = "block";
                    mobileLogout.style.display = "none";
                    dashboardLink.style.display = "none";
                    mobileDashboardLink.style.display = "none";
                }
            })
            .catch(error => {
                console.error('Error checking session:', error);
            });
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
    new ModernNavbar();
});
