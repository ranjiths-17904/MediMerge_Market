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
    }

    createNavbar() {
        const navbarHTML = `
            <header class="modern-header">
                <div class="nav-container">
                    <!-- Logo Section -->
                    <div class="logo-section">
                        <a href="medico.html" class="logo-link">
                            <img src="./Images/MEDI_MERGE_LOGO.png" alt="MediMerge" class="logo-img">
                            <span class="logo-text ">Medi<span class="logo-accent">Merge</span></span>
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
                    background: #ffffff;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                    position: sticky;
                    top: 0;
                    z-index: 1000;
                    backdrop-filter: blur(8px);
                }

                .nav-container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 0 20px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    height: 70px;
                }

                .logo-section {
                    display: flex;
                    align-items: center;
                }

                .logo-link {
                    display: flex;
                    align-items: center;
                    text-decoration: none;
                    color: white;
                }

                .logo-img {
                    width: 40px;
                    height: 40px;
                    margin-right: 10px;
                }

                .logo-text {
                    font-size: 24px;
                    font-weight: 700;
                    color: white;
                }

                .logo-accent {
                    color: #11b671;
                }

                .desktop-nav {
                    display: flex;
                    gap: 30px;
                }

                .nav-link {
                    color: #333;
                    text-decoration: none;
                    font-weight: 500;
                    font-size: 16px;
                    transition: all 0.3s ease;
                    position: relative;
                }

                .nav-link:hover {
                    color: #11b671;
                    transform: translateY(-2px);
                }

                .nav-link::after {
                    content: '';
                    position: absolute;
                    bottom: -5px;
                    left: 0;
                    width: 0;
                    height: 2px;
                    background: #11b671;
                    transition: width 0.3s ease;
                }

                .nav-link:hover::after {
                    width: 100%;
                }

                .search-section {
                    flex: 1;
                    max-width: 400px;
                    margin: 0 20px;
                }

                .search-container {
                    position: relative;
                    display: flex;
                    align-items: center;
                }

                .search-input {
                    width: 100%;
                    padding: 12px 45px 12px 15px;
                    border: none;
                    border-radius: 25px;
                    background: #f5f5f5;
                    font-size: 14px;
                    outline: none;
                    transition: all 0.3s ease;
                }

                .search-input:focus {
                    background: #ffffff;
                    box-shadow: 0 0 0 3px rgba(17, 182, 113, 0.2);
                }

                .search-btn {
                    position: absolute;
                    right: 5px;
                    background: #11b671;
                    border: none;
                    border-radius: 50%;
                    width: 35px;
                    height: 35px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .search-btn:hover {
                    background: #0ea55d;
                    transform: scale(1.1);
                }

                .user-actions {
                    display: flex;
                    align-items: center;
                    gap: 20px;
                }

                .cart-link {
                    position: relative;
                    color: #333;
                    font-size: 20px;
                    text-decoration: none;
                    transition: all 0.3s ease;
                }

                .cart-link:hover {
                    color: #11b671;
                    transform: scale(1.1);
                }

                .cart-count {
                    position: absolute;
                    top: -8px;
                    right: -8px;
                    background: #ff4757;
                    color: white;
                    border-radius: 50%;
                    width: 20px;
                    height: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 12px;
                    font-weight: bold;
                }

                .auth-section {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }

                .auth-link {
                    color: #333;
                    text-decoration: none;
                    font-weight: 500;
                    padding: 8px 16px;
                    border-radius: 20px;
                    transition: all 0.3s ease;
                    border: 2px solid transparent;
                }

                .auth-link:hover {
                    background: rgba(255,255,255,0.1);
                    border-color: rgba(255,255,255,0.3);
                }

                .logout-link {
                    background: rgba(255,71,87,0.8);
                }

                .logout-link:hover {
                    background: #ff4757;
                }

                /* Toast popups */
                .mm-toast{position:fixed;right:20px;top:20px;z-index:2000;display:flex;flex-direction:column;gap:10px}
                .mm-toast .item{background:#333;color:#fff;padding:12px 16px;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,.2);opacity:.95}

                .username-display {
                    color: #333;
                    font-weight: 600;
                    font-size: 14px;
                    display: none;
                }

                .mobile-menu-btn {
                    display: none;
                    flex-direction: column;
                    background: none;
                    border: none;
                    cursor: pointer;
                    padding: 5px;
                }

                .mobile-menu-btn span {
                    width: 25px;
                    height: 3px;
                    background: white;
                    margin: 3px 0;
                    transition: 0.3s;
                    border-radius: 2px;
                }

                .mobile-nav {
                    display: none;
                    background: rgba(102, 126, 234, 0.95);
                    padding: 20px;
                    backdrop-filter: blur(10px);
                }

                .mobile-nav-link {
                    display: block;
                    color: white;
                    text-decoration: none;
                    padding: 15px 0;
                    border-bottom: 1px solid rgba(255,255,255,0.1);
                    font-weight: 500;
                    transition: all 0.3s ease;
                }

                .mobile-nav-link:hover {
                    color: #11b671;
                    padding-left: 10px;
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
                    }

                    .logo-text {
                        font-size: 20px;
                    }

                    .user-actions {
                        gap: 15px;
                    }
                }

                @media (max-width: 480px) {
                    .nav-container {
                        height: 60px;
                    }

                    .logo-img {
                        width: 35px;
                        height: 35px;
                    }

                    .logo-text {
                        font-size: 18px;
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

                if (data.username) {
                    usernameDisplay.textContent = `Hello, ${data.username}`;
                    usernameDisplay.style.display = "inline";
                    signInLink.style.display = "none";
                    logoutLink.style.display = "inline";
                    mobileSignIn.style.display = "none";
                    mobileLogout.style.display = "block";
                } else {
                    usernameDisplay.style.display = "none";
                    signInLink.style.display = "inline";
                    logoutLink.style.display = "none";
                    mobileSignIn.style.display = "block";
                    mobileLogout.style.display = "none";
                }
            })
            .catch(error => console.error('Error fetching session:', error));
    }

    setupMobileMenu() {
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileNav = document.getElementById('mobile-nav');
        const spans = mobileBtn.querySelectorAll('span');

        mobileBtn.addEventListener('click', () => {
            mobileNav.style.display = mobileNav.style.display === 'block' ? 'none' : 'block';
            
            // Animate hamburger to X
            spans.forEach((span, index) => {
                if (mobileNav.style.display === 'block') {
                    if (index === 0) span.style.transform = 'rotate(45deg) translate(5px, 5px)';
                    if (index === 1) span.style.opacity = '0';
                    if (index === 2) span.style.transform = 'rotate(-45deg) translate(7px, -6px)';
                } else {
                    span.style.transform = 'none';
                    span.style.opacity = '1';
                }
            });
        });
    }

    setupSearch() {
        const searchInput = document.querySelector('.search-input');
        const searchBtn = document.querySelector('.search-btn');
        if(!searchInput || !searchBtn) return;
        searchBtn.addEventListener('click', () => {
            const query = searchInput.value.trim();
            if (query) {
                window.location.href = `product.html?search=${encodeURIComponent(query)}`;
            }
        });
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') searchBtn.click();
        });
    }

    updateCartCount(count) {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = count;
        }
    }

    showToast(message){
        let container=document.querySelector('.mm-toast');
        if(!container){
            container=document.createElement('div');
            container.className='mm-toast';
            document.body.appendChild(container);
        }
        const el=document.createElement('div');
        el.className='item';
        el.textContent=message;
        container.appendChild(el);
        setTimeout(()=>{
            el.style.transform='translateX(120%)';
            el.style.transition='transform .3s ease, opacity .3s ease';
            el.style.opacity='0';
            setTimeout(()=>container.removeChild(el),300);
        },2500);
    }
}

// Initialize navbar when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const nb = new ModernNavbar();
    // initialize cart count from storage
    try{
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const total = cart.reduce((s,i)=>s + (i.quantity||0), 0);
        nb.updateCartCount(total);
    }catch(e){}
});
