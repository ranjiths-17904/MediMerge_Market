# MediMerge - Healthcare E-commerce Platform

A modern, responsive healthcare product and medicine e-commerce website built with PHP, MySQL, and modern web technologies.

## 🚀 Quick Start

### 1. Start XAMPP
- Open XAMPP Control Panel
- Start **Apache** and **MySQL** services

### 2. Setup Database
```
http://localhost/MediMerge-Market/mini%20world%20project/setup_database.php
```

### 3. Access Website
```
http://localhost/MediMerge-Market/mini%20world%20project/medico.html
```

## 👥 Default Login

**Admin Account:**
- Username: `TheAdmin`
- Password: `Admin@MM`

## ✨ Features

- 🛍️ **Product Catalog**: Browse healthcare products
- 🛒 **Shopping Cart**: Add/remove items
- 💳 **Checkout System**: Complete purchase process
- 👨‍💼 **Admin Panel**: Manage products, orders, users
- 📱 **Responsive Design**: Works on all devices
- 🔐 **User Authentication**: Secure login/signup system

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+, MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Database**: MySQL 5.7+
- **Server**: Apache (XAMPP)
- **Icons**: Font Awesome 6.0

## 📁 Project Structure

```
mini world project/
├── config/
│   └── database.php          # Database configuration
├── admin/
│   ├── dashboard.html        # Admin dashboard
│   ├── product_api.php       # Product management API
│   └── orders_api.php        # Order management API
├── api/
│   ├── cart_api.php          # Cart management API
│   └── checkout_api.php      # Checkout API
├── auth/
│   ├── login_handler.php     # Login processing
│   └── signup_handler.php    # Registration processing
├── Images/                   # Product images
├── Style/                    # CSS stylesheets
├── medico.html              # Homepage
├── product.html             # Products page
├── cart.html                # Shopping cart
├── checkout.html            # Checkout page
├── login.php                # Login page
├── signup.php               # Registration page
├── setup_database.php       # Database setup
├── test_connection.php      # Connection test
├── deploy.bat               # Windows deployment script
└── DEPLOYMENT.md            # Detailed deployment guide
```

## 🔧 Installation

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Modern web browser
- PHP 7.4+ with MySQL extension

### Steps
1. **Download** project files
2. **Extract** to XAMPP htdocs folder
3. **Start** XAMPP services
4. **Run** database setup
5. **Access** website

## 🌐 URLs

- **Home**: `medico.html`
- **Products**: `product.html`
- **Cart**: `cart.html`
- **Checkout**: `checkout.html`
- **Login**: `login.php`
- **Signup**: `signup.php`
- **Admin**: `admin/dashboard.html`

## 🚨 Troubleshooting

### Common Issues
1. **404 Error**: Check file paths include "mini world project"
2. **Database Error**: Ensure MySQL is running
3. **CORS Error**: Access via `http://localhost` not `file://`

### Solutions
- Run `test_connection.php` to diagnose issues
- Check XAMPP services are running
- Verify database setup completed

## 📱 Responsive Design

- ✅ Desktop (1920x1080+)
- ✅ Laptop (1366x768+)
- ✅ Tablet (768x1024+)
- ✅ Mobile (375x667+)

## 🔒 Security Features

- SQL injection prevention
- XSS protection
- Session management
- Input validation
- Password hashing

## 📊 Admin Features

- **Product Management**: Add/Edit/Delete products
- **Order Management**: Update order statuses
- **User Management**: View registered users
- **Inventory Control**: Monitor stock levels

## 🚀 Performance

- Optimized database queries
- Prepared statements
- Efficient image loading
- Minified assets

## 📞 Support

- **Documentation**: `DEPLOYMENT.md`
- **Quick Start**: `GET_STARTED.md`
- **Deployment**: `deploy.bat` (Windows)

## 📝 License

This project is for educational and demonstration purposes.

---

**🎉 Ready to deploy? Run `deploy.bat` or follow `DEPLOYMENT.md` for detailed instructions!**
