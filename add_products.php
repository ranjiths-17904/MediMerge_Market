<?php
// Simple script to add sample products to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medico";

echo "<h2>Adding Sample Products to Database</h2>";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ Connection failed: " . $conn->connect_error . "</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Check if products table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'products'");
    if ($tableCheck->num_rows == 0) {
        echo "<p style='color: red;'>❌ Products table does not exist!</p>";
        exit;
    }
    
    // Clear existing products first
    $conn->query("DELETE FROM products");
    echo "<p>🗑️ Cleared existing products</p>";
    
    // Sample products data
    $products = [
        [
            'name' => 'Aspirin 500mg',
            'description' => 'Pain reliever and fever reducer. Effective for headaches, muscle pain, and reducing fever.',
            'price' => 5.00,
            'category' => 'Pain Relief',
            'image' => './Images/aspirin.jpeg',
            'stock' => 100
        ],
        [
            'name' => 'Paracetamol 500mg',
            'description' => 'Pain reliever and fever reducer. Safe and effective for adults and children.',
            'price' => 8.00,
            'category' => 'Pain Relief',
            'image' => './Images/pills.png',
            'stock' => 150
        ],
        [
            'name' => 'Vicks VapoRub',
            'description' => 'Topical decongestant for chest and throat congestion. Provides relief from cold symptoms.',
            'price' => 120.00,
            'category' => 'Cold & Cough',
            'image' => './Images/vicks.png',
            'stock' => 75
        ],
        [
            'name' => 'Insulin Injection',
            'description' => 'Fast-acting insulin for diabetes management. Helps control blood sugar levels.',
            'price' => 450.00,
            'category' => 'Diabetes',
            'image' => './Images/insulin.jpg',
            'stock' => 50
        ],
        [
            'name' => 'Creatine Monohydrate',
            'description' => 'Muscle building supplement for athletes and fitness enthusiasts.',
            'price' => 800.00,
            'category' => 'Vitamins',
            'image' => './Images/creatine.jpeg',
            'stock' => 60
        ],
        [
            'name' => 'Blood Pressure Monitor',
            'description' => 'Digital blood pressure monitor for home use. Accurate readings for health monitoring.',
            'price' => 1200.00,
            'category' => 'First Aid',
            'image' => './Images/bp.png',
            'stock' => 25
        ],
        [
            'name' => 'Vitamin C 1000mg',
            'description' => 'Immune system support and antioxidant. Helps maintain overall health and wellness.',
            'price' => 150.00,
            'category' => 'Vitamins',
            'image' => './Images/pills.png',
            'stock' => 200
        ],
        [
            'name' => 'First Aid Kit',
            'description' => 'Complete first aid kit with bandages, antiseptic, and essential medical supplies.',
            'price' => 500.00,
            'category' => 'First Aid',
            'image' => './Images/pills.png',
            'stock' => 30
        ],
        [
            'name' => 'Cough Syrup',
            'description' => 'Effective cough suppressant for dry coughs. Provides relief and helps with sleep.',
            'price' => 180.00,
            'category' => 'Cold & Cough',
            'image' => './Images/cold.jpeg',
            'stock' => 80
        ],
        [
            'name' => 'Multivitamin Tablets',
            'description' => 'Daily multivitamin supplement for overall health and wellness support.',
            'price' => 250.00,
            'category' => 'Vitamins',
            'image' => './Images/pills.png',
            'stock' => 120
        ]
    ];
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($products as $product) {
        $sql = "INSERT INTO products (name, description, price, category, image, stock) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssdssi", 
                $product['name'], 
                $product['description'], 
                $product['price'], 
                $product['category'], 
                $product['image'], 
                $product['stock']
            );
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✅ Added: " . $product['name'] . " - ₹" . $product['price'] . "</p>";
                $successCount++;
            } else {
                echo "<p style='color: red;'>❌ Error adding " . $product['name'] . ": " . $stmt->error . "</p>";
                $errorCount++;
            }
            $stmt->close();
        } else {
            echo "<p style='color: red;'>❌ Failed to prepare statement for " . $product['name'] . "</p>";
            $errorCount++;
        }
    }
    
    echo "<hr>";
    echo "<h3>Summary</h3>";
    echo "<p>✅ Successfully added: " . $successCount . " products</p>";
    echo "<p>❌ Failed to add: " . $errorCount . " products</p>";
    
    // Verify products were added
    $result = $conn->query("SELECT COUNT(*) as count FROM products");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>📦 Total products in database: " . $row['count'] . "</p>";
    }
    
    $conn->close();
    
    echo "<hr>";
    echo "<p><a href='product.html' style='background: #11b671; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Go to Products Page</a></p>";
    echo "<p><a href='medico.html' style='color: #11b671;'>← Back to Home</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}
?>
