# MediMerge Website - Deployment Guide

## 🚀 Quick Start (3 Simple Steps)

### Step 1: Setup Database
1. **Start XAMPP**: Ensure Apache and MySQL are running
2. **Access Setup**: Go to `http://localhost/MediMerge-Market/mini%20world%20project/setup_database.php`
3. **Verify**: You should see "Database setup completed!" message

### Step 2: Test Connection
1. **Check Database**: Go to `http://localhost/MediMerge-Market/mini%20world%20project/test_connection.php`
2. **Verify**: Should show "Database connection successful!"

### Step 3: Access Website
1. **Main Site**: Go to `http://localhost/MediMerge-Market/mini%20world%20project/medico.html`
2. **Admin Panel**: Login with `TheAdmin` / `Admin@MM`

---

## 📋 Prerequisites

- **XAMPP** (Apache + MySQL + PHP)
- **PHP 7.4+** with MySQL extension
- **Modern Web Browser** (Chrome, Firefox, Safari, Edge)

---

## 🗄️ Database Configuration

### Default Settings
- **Host**: `localhost`
- **Username**: `root`
- **Password**: `` (empty)
- **Database**: `medico`

### Custom Configuration
Edit `config/database.php`:
```php
$servername = "your_host";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";
```

---

## 🔧 Installation Steps

### 1. Download & Extract
```bash
# Clone or download the project
cd /path/to/xampp/htdocs/
# Extract to: MediMerge-Market/mini world project/
```

### 2. Start XAMPP Services
- Open XAMPP Control Panel
- Start **Apache** (Port 80/443)
- Start **MySQL** (Port 3306)

### 3. Setup Database
```bash
# Navigate to setup script
http://localhost/MediMerge-Market/mini%20world%20project/setup_database.php
```

### 4. Verify Installation
- Database tables created
- Sample products added
- Admin user created

---

## 👥 Default Users

### Admin Account
- **Username**: `TheAdmin`
- **Password**: `Admin@MM`
- **Email**: `AdminMM@gmail.com`

### Create New Users
1. Go to `http://localhost/MediMerge-Market/mini%20world%20project/signup.php`
2. Fill registration form
3. Login at `http://localhost/MediMerge-Market/mini%20world%20project/login.php`

---

## 🛍️ Features

### User Features
- ✅ **Browse Products**: View all available products
- ✅ **Search & Filter**: Find products by category/name
- ✅ **Shopping Cart**: Add/remove items
- ✅ **Checkout**: Complete purchase process
- ✅ **Order Tracking**: Monitor order status

### Admin Features
- ✅ **Product Management**: Add/Edit/Delete products
- ✅ **Order Management**: Update order statuses
- ✅ **User Management**: View registered users
- ✅ **Inventory Control**: Monitor stock levels

---

## 🔗 Important URLs

### Main Pages
- **Home**: `medico.html`
- **Products**: `product.html`
- **Cart**: `cart.html`
- **Checkout**: `checkout.html`
- **Login**: `login.php`
- **Signup**: `signup.php`

### Admin Pages
- **Dashboard**: `admin/dashboard.html`
- **Product API**: `admin/product_api.php`
- **Orders API**: `admin/orders_api.php`

### API Endpoints
- **Products**: `products_api.php`
- **Cart**: `api/cart_api.php`
- **Checkout**: `api/checkout_api.php`
- **Session**: `session.php`

---

## 🚨 Troubleshooting

### Common Issues

#### 1. 404 Error - Page Not Found
**Problem**: `http://localhost/MediMerge-Market/setup_database.php` returns 404
**Solution**: Use correct path: `http://localhost/MediMerge-Market/mini%20world%20project/setup_database.php`

#### 2. Database Connection Failed
**Problem**: "Connection failed" error
**Solution**: 
- Check XAMPP MySQL is running
- Verify database credentials in `config/database.php`
- Create database manually: `CREATE DATABASE medico;`

#### 3. CORS Policy Error
**Problem**: JavaScript fetch errors
**Solution**: 
- Access via `http://localhost` not `file://`
- Ensure Apache is running
- Check `.htaccess` file exists

#### 4. Session Not Working
**Problem**: Login/logout issues
**Solution**:
- Check PHP session configuration
- Verify `session.php` file exists
- Clear browser cookies

#### 5. Products Not Loading
**Problem**: Empty product list
**Solution**:
- Run `setup_database.php` first
- Check `products_api.php` for errors
- Verify database tables exist

---

## 🔒 Security Considerations

### Production Deployment
- **Change Default Passwords**: Update admin credentials
- **Database Security**: Use strong passwords
- **HTTPS**: Enable SSL/TLS
- **File Permissions**: Restrict access to config files
- **Input Validation**: All user inputs are sanitized

### Development Environment
- **Local Access Only**: Don't expose to internet
- **Debug Mode**: Enable error reporting for development
- **Test Data**: Use sample data only

---

## 📱 Responsive Design

### Supported Devices
- ✅ **Desktop**: 1920x1080 and above
- ✅ **Laptop**: 1366x768 and above
- ✅ **Tablet**: 768x1024 and above
- ✅ **Mobile**: 375x667 and above

### Browser Support
- ✅ **Chrome**: 90+
- ✅ **Firefox**: 88+
- ✅ **Safari**: 14+
- ✅ **Edge**: 90+

---

## 🚀 Performance Optimization

### Database
- Indexes on frequently queried columns
- Prepared statements for security
- Connection pooling

### Frontend
- Optimized images
- Minified CSS/JS
- Lazy loading for products

---

## 📊 Monitoring & Maintenance

### Regular Tasks
- **Database Backups**: Weekly backups
- **Log Monitoring**: Check error logs
- **Performance**: Monitor response times
- **Security**: Update dependencies

### Error Logs
- **Apache Logs**: `xampp/apache/logs/`
- **PHP Errors**: Check browser console
- **Database Logs**: MySQL error log

---

## 🌐 Production Deployment

### Server Requirements
- **Web Server**: Apache 2.4+ or Nginx
- **PHP**: 7.4+ with extensions
- **Database**: MySQL 5.7+ or MariaDB 10.2+
- **SSL**: HTTPS certificate

### Deployment Steps
1. **Upload Files**: Transfer to web server
2. **Configure Database**: Update connection settings
3. **Set Permissions**: Configure file/directory permissions
4. **SSL Setup**: Enable HTTPS
5. **Domain Configuration**: Point domain to server
6. **Testing**: Verify all functionality works

---

## 📞 Support

### Documentation
- **README.md**: Project overview
- **GET_STARTED.md**: Quick start guide
- **DEPLOYMENT.md**: This deployment guide

### Common Commands
```bash
# Check XAMPP status
xampp-control.exe

# View Apache logs
tail -f /xampp/apache/logs/access.log

# Check MySQL status
mysql -u root -p -e "SHOW PROCESSLIST;"
```

---

## ✅ Verification Checklist

- [ ] XAMPP Apache running
- [ ] XAMPP MySQL running
- [ ] Database setup completed
- [ ] Sample products loaded
- [ ] Admin user created
- [ ] Main website accessible
- [ ] Admin dashboard working
- [ ] Cart functionality working
- [ ] Checkout process working
- [ ] Responsive design working

---

**🎉 Congratulations! Your MediMerge website is now fully operational!**

For additional support or questions, please refer to the project documentation or create an issue in the project repository.
