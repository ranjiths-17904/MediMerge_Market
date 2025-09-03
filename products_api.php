<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once 'config/database.php';

try {
    // Check if products table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'products'");
    if ($tableCheck->num_rows == 0) {
        // Create products table if it doesn't exist
        $createTable = "CREATE TABLE IF NOT EXISTS products (
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
        
        if (!$conn->query($createTable)) {
            throw new Exception("Failed to create products table: " . $conn->error);
        }
        
        // Insert default products
        $defaultProducts = [
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
            ]
        ];
        
        foreach ($defaultProducts as $product) {
            $insert_sql = "INSERT INTO products (name, description, price, category, image, stock) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssdssi", $product['name'], $product['description'], $product['price'], $product['category'], $product['image'], $product['stock']);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    $sql = "SELECT id, name, description, price, category, image, stock FROM products ORDER BY id DESC";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $products = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $row['price'] = (float)$row['price'];
            $row['stock'] = (int)$row['stock'];
            $row['id'] = (int)$row['id'];
            $products[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($products),
        'products' => $products
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Exception occurred',
        'message' => $e->getMessage(),
        'products' => []
    ]);
} finally {
    if (isset($conn)) {
        closeConnection($conn);
    }
}
?>


