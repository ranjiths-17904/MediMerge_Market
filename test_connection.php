<?php
// Test database connection
require_once 'config/database.php';

echo "<h2>Database Connection Test</h2>";

if ($conn->ping()) {
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test if tables exist
    $tables = ['users', 'products', 'orders'];
    $all_tables_exist = true;
    
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Table '$table' exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' missing</p>";
            $all_tables_exist = false;
        }
    }
    
    if ($all_tables_exist) {
        // Check if sample data exists
        $result = $conn->query("SELECT COUNT(*) as count FROM products");
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            echo "<p style='color: green;'>✅ Products table has {$row['count']} products</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Products table is empty - run setup_database.php</p>";
        }
        
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            echo "<p style='color: green;'>✅ Users table has {$row['count']} users</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Users table is empty - run setup_database.php</p>";
        }
        
        echo "<p><a href='setup_database.php'>🔧 Run Database Setup</a></p>";
        echo "<p><a href='medico.html'>🏠 Go to Homepage</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Some tables are missing. Please run setup_database.php</p>";
        echo "<p><a href='setup_database.php'>🔧 Run Database Setup</a></p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL service is running</li>";
    echo "<li>Database 'medico' exists</li>";
    echo "<li>Database credentials in config/database.php</li>";
    echo "</ul>";
}

closeConnection($conn);
?>
