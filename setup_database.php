<?php
// Include database configuration
require_once 'config/database.php';

// Create users table if it doesn't exist
$users_sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(20),
    country VARCHAR(100),
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($users_sql) === TRUE) {
    echo "Users table created successfully or already exists<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

// Create products table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(100),
    image VARCHAR(500),
    stock INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Products table created successfully or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Insert sample products if table is empty
$check_sql = "SELECT COUNT(*) as count FROM products";
$result = $conn->query($check_sql);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $sample_products = [
        [
            'name' => 'Paracetamol 500mg',
            'description' => 'Effective pain relief and fever reduction. Safe for adults and children when used as directed.',
            'price' => 7.00,
            'category' => 'Pain Relief',
            'image' => './Images/pills.png',
            'stock' => 50
        ],
        [
            'name' => 'I-Pill Emergency Contraceptive',
            'description' => 'Emergency contraceptive pill for women. Use within 72 hours of unprotected intercourse.',
            'price' => 10.00,
            'category' => 'First Aid',
            'image' => './Images/i-pill.png',
            'stock' => 25
        ],
        [
            'name' => 'Equate Cold & Flu Relief',
            'description' => 'Multi-symptom relief for cold, flu, and congestion. Helps with runny nose, fever, and body aches.',
            'price' => 9.00,
            'category' => 'Cold & Cough',
            'image' => './Images/cold.png',
            'stock' => 35
        ],
        [
            'name' => 'Diabetes Management',
            'description' => 'Blood sugar control tablets. Helps maintain healthy glucose levels for diabetic patients.',
            'price' => 15.00,
            'category' => 'Diabetes',
            'image' => './Images/Diabites.png',
            'stock' => 20
        ],
        [
            'name' => 'Vitamin C 1000mg',
            'description' => 'High potency Vitamin C supplement. Boosts immunity and supports overall health.',
            'price' => 12.00,
            'category' => 'Vitamins',
            'image' => './Images/protien.png',
            'stock' => 40
        ],
        [
            'name' => 'Aspirin 100mg',
            'description' => 'Low-dose aspirin for heart health. Reduces risk of heart attack and stroke.',
            'price' => 8.00,
            'category' => 'Pain Relief',
            'image' => './Images/aspirin.jpeg',
            'stock' => 30
        ],
        [
            'name' => 'Vicks VapoRub',
            'description' => 'Topical decongestant for chest and throat congestion. Provides relief from cold symptoms.',
            'price' => 6.00,
            'category' => 'Cold & Cough',
            'image' => './Images/vicks.png',
            'stock' => 45
        ],
        [
            'name' => 'Insulin Injection',
            'description' => 'Fast-acting insulin for diabetes management. Helps control blood sugar levels.',
            'price' => 25.00,
            'category' => 'Diabetes',
            'image' => './Images/insulin.jpg',
            'stock' => 15
        ],
        [
            'name' => 'Creatine Monohydrate',
            'description' => 'Muscle building supplement for athletes and fitness enthusiasts.',
            'price' => 18.00,
            'category' => 'Vitamins',
            'image' => './Images/creatine.jpeg',
            'stock' => 30
        ],
        [
            'name' => 'Blood Pressure Monitor',
            'description' => 'Digital blood pressure monitor for home use. Accurate readings for health monitoring.',
            'price' => 45.00,
            'category' => 'First Aid',
            'image' => './Images/bp.png',
            'stock' => 20
        ]
    ];

    foreach ($sample_products as $product) {
        $insert_sql = "INSERT INTO products (name, description, price, category, image, stock) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssdssi", $product['name'], $product['description'], $product['price'], $product['category'], $product['image'], $product['stock']);
        
        if ($stmt->execute()) {
            echo "Added product: " . $product['name'] . "<br>";
        } else {
            echo "Error adding product: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }
    echo "Sample products added successfully!<br>";
} else {
    echo "Products table already contains data.<br>";
}

// Create orders table if it doesn't exist
$orders_sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(100) UNIQUE NOT NULL,
    user_id INT(11),
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50) NOT NULL,
    customer_address TEXT NOT NULL,
    items JSON NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    transaction_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)";

if ($conn->query($orders_sql) === TRUE) {
    echo "Orders table created successfully or already exists<br>";
} else {
    echo "Error creating orders table: " . $conn->error . "<br>";
}

// Create admin_users table for admin functionality
$admin_users_sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($admin_users_sql) === TRUE) {
    echo "Admin users table created successfully or already exists<br>";
} else {
    echo "Error creating admin users table: " . $conn->error . "<br>";
}

// Create chat_messages table for chatbot
$chat_sql = "CREATE TABLE IF NOT EXISTS chat_messages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    session_id VARCHAR(255),
    message TEXT NOT NULL,
    response TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)";

if ($conn->query($chat_sql) === TRUE) {
    echo "Chat messages table created successfully or already exists<br>";
} else {
    echo "Error creating chat messages table: " . $conn->error . "<br>";
}

// Create notifications table
$notifications_sql = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    user_type ENUM('user', 'admin') DEFAULT 'user',
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    order_id INT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($notifications_sql) === TRUE) {
    echo "Notifications table created successfully or already exists<br>";
} else {
    echo "Error creating notifications table: " . $conn->error . "<br>";
}

// Ensure admin user exists with correct credentials
$adminCheck = $conn->prepare("SELECT id FROM users WHERE username=?");
$adminUser = 'TheAdmin';
$adminCheck->bind_param('s', $adminUser);
$adminCheck->execute();
$adminCheck->store_result();
if($adminCheck->num_rows === 0){
    $adminEmail = 'AdminMM@gmail.com';
    $adminPass = password_hash('Admin@MM', PASSWORD_DEFAULT);
    $ins = $conn->prepare("INSERT INTO users (email, username, password, is_admin) VALUES (?,?,?,TRUE)");
    if($ins){
        $ins->bind_param('sss', $adminEmail, $adminUser, $adminPass);
        if($ins->execute()){
            echo "Admin user created successfully!<br>";
            echo "Email: AdminMM@gmail.com<br>";
            echo "Username: TheAdmin<br>";
            echo "Password: Admin@MM<br>";
        }
        $ins->close();
    }
} else {
    echo "Admin user already exists.<br>";
}
$adminCheck->close();

// Create admin user in admin_users table
$adminUserCheck = $conn->prepare("SELECT id FROM admin_users WHERE username=?");
$adminUserCheck->bind_param('s', $adminUser);
$adminUserCheck->execute();
$adminUserCheck->store_result();
if($adminUserCheck->num_rows === 0){
    $adminEmail = 'AdminMM@gmail.com';
    $adminPass = password_hash('Admin@MM', PASSWORD_DEFAULT);
    $ins = $conn->prepare("INSERT INTO admin_users (username, email, password, role) VALUES (?,?,?,'super_admin')");
    if($ins){
        $ins->bind_param('sss', $adminUser, $adminEmail, $adminPass);
        if($ins->execute()){
            echo "Admin user added to admin_users table successfully!<br>";
        }
        $ins->close();
    }
} else {
    echo "Admin user already exists in admin_users table.<br>";
}
$adminUserCheck->close();

closeConnection($conn);
echo "<br>Database setup completed! <a href='medico.html'>Go to Home</a>";
?>
    