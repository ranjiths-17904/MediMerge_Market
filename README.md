# MediMerge - Healthcare E-commerce Platform

A comprehensive healthcare e-commerce platform built with HTML, CSS, JavaScript, and PHP. Features a responsive design, user authentication, product management, shopping cart, and order processing.

## 🚀 Features

### User Features
- **Product Browsing**: Browse through a wide range of healthcare products
- **Shopping Cart**: Add products to cart with quantity management
- **User Authentication**: Secure login and signup system
- **Checkout Process**: Complete order placement with multiple payment options
- **Order Tracking**: View order history and status
- **Responsive Design**: Mobile-first design for all devices

### Admin Features
- **Product Management**: Add, edit, and delete products
- **Order Management**: View and manage customer orders
- **User Management**: Monitor user accounts
- **Dashboard Analytics**: Overview of sales and orders

## 🛠️ Technical Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Styling**: Custom CSS with responsive design
- **Icons**: Font Awesome 6.0
- **Server**: XAMPP (Apache + MySQL + PHP)

## 📱 Responsive Design

The platform is fully responsive and optimized for:
- **Desktop**: 1200px and above
- **Tablet**: 768px - 1199px
- **Mobile**: 320px - 767px
- **Small Mobile**: Below 320px

## 🔧 Installation & Setup

### Prerequisites
- XAMPP (or similar local server stack)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### Setup Instructions

1. **Clone/Download the project**
   ```bash
   # Place the project in your XAMPP htdocs folder
   C:\xampp\htdocs\MediMerge-Market\
   ```

2. **Start XAMPP Services**
   - Start Apache and MySQL services
   - Ensure both services are running

3. **Database Setup**
   ```bash
   # Open your browser and navigate to:
   http://localhost/MediMerge-Market/setup_database.php
   ```
   This will create the database and required tables.

4. **Access the Application**
   ```bash
   # Main application URL:
   http://localhost/MediMerge-Market/medico.html
   ```

## 🗄️ Database Structure

### Tables
- **users**: User accounts and authentication
- **products**: Product catalog with images and pricing
- **orders**: Customer orders and payment information

### Admin Credentials
- **Email**: AdminMM@gmail.com
- **Username**: TheAdmin
- **Password**: Admin@MM

## 📁 File Structure

```
MediMerge-Market/
├── Images/                 # Product and logo images
├── Style/                  # CSS stylesheets
├── medico.html            # Main homepage
├── product.html           # Product catalog page
├── cart.html              # Shopping cart page
├── checkout.html          # Checkout process
├── confirmation.html      # Order confirmation
├── login.php              # User authentication
├── signup.php             # User registration
├── dashboard.php          # Admin dashboard
├── products_api.php       # Product API endpoint
├── saveOrder.php          # Order processing
├── navbar.js              # Navigation component
├── setup_database.php     # Database initialization
└── test_connection.php    # Database connection test
```

## 🚨 Recent Fixes & Improvements

### 1. Checkout Error Resolution
- **Issue**: JSON parsing error in checkout process
- **Fix**: Updated `saveOrder.php` with proper headers and error handling
- **Added**: CORS headers, output buffering, and proper HTTP status codes

### 2. Responsive Design Implementation
- **Product Page**: Fully responsive with mobile-optimized layout
- **Checkout Page**: Mobile-friendly form design and payment methods
- **Cart Page**: Responsive cart management interface
- **Navigation**: Mobile-first navbar with hamburger menu

### 3. Product Loading Issues
- **Issue**: Products not displaying after login
- **Fix**: Improved product loading logic and error handling
- **Added**: Loading states and fallback images

### 4. Mobile Experience Enhancement
- **Touch-friendly**: Optimized button sizes and spacing
- **Responsive Grids**: Flexible layouts for all screen sizes
- **Mobile Navigation**: Collapsible menu with smooth animations

### 5. Code Quality Improvements
- **Error Handling**: Better error messages and validation
- **Performance**: Optimized CSS and JavaScript
- **Accessibility**: Improved form labels and navigation

## 🎨 Design Features

### Color Scheme
- **Primary**: #11b671 (Green)
- **Secondary**: #667eea (Blue)
- **Accent**: #ff4757 (Red)
- **Neutral**: #f8f9fa, #e9ecef

### Typography
- **Font Family**: Inter (system fallback)
- **Responsive Sizing**: Using CSS clamp() for fluid typography
- **Hierarchy**: Clear visual hierarchy with proper contrast

### Components
- **Cards**: Modern card design with shadows and hover effects
- **Buttons**: Consistent button styling with hover animations
- **Forms**: Clean form design with focus states
- **Navigation**: Sticky navigation with backdrop blur effects

## 📱 Mobile Optimization

### Responsive Breakpoints
```css
/* Large Desktop */
@media (min-width: 1200px) { ... }

/* Desktop */
@media (max-width: 1199px) { ... }

/* Tablet */
@media (max-width: 1024px) { ... }

/* Mobile */
@media (max-width: 768px) { ... }

/* Small Mobile */
@media (max-width: 480px) { ... }

/* Extra Small Mobile */
@media (max-width: 360px) { ... }
```

### Mobile-First Features
- Touch-friendly button sizes (minimum 44px)
- Swipe-friendly navigation
- Optimized form inputs for mobile
- Responsive image handling

## 🔒 Security Features

- **SQL Injection Protection**: Prepared statements
- **XSS Prevention**: Input sanitization
- **Session Management**: Secure session handling
- **Password Hashing**: Bcrypt password encryption

## 🧪 Testing

### Database Connection Test
Visit `test_connection.php` to verify:
- Database connectivity
- Table structure
- Admin user existence
- PHP configuration

### Browser Testing
Tested on:
- Chrome (Desktop & Mobile)
- Firefox (Desktop & Mobile)
- Safari (Desktop & Mobile)
- Edge (Desktop)

## 🚀 Performance Optimizations

- **CSS Optimization**: Minimal CSS with efficient selectors
- **JavaScript**: Modular code with event delegation
- **Images**: Optimized image sizes and formats
- **Caching**: Browser caching for static assets

## 📋 Browser Support

- **Modern Browsers**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Mobile Browsers**: iOS Safari 14+, Chrome Mobile 90+
- **Legacy Support**: IE11+ (with polyfills)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- **Email**: support@medimerge.com
- **Documentation**: Check this README and inline code comments
- **Issues**: Report bugs through the issue tracker

## 🔄 Updates & Maintenance

### Regular Maintenance
- Database backups
- Security updates
- Performance monitoring
- User feedback collection

### Future Enhancements
- Payment gateway integration
- Advanced search and filtering
- User reviews and ratings
- Inventory management system
- Analytics dashboard

---

**MediMerge** - Your Health, Our Priority 🏥

*Built with ❤️ for better healthcare accessibility*
