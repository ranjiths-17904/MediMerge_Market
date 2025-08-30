<?php
// Test database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medico";

echo "<h2>Testing Database Connection</h2>";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ Connection failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Database connection successful!</p>";
        
        // Test products table
        $sql = "SELECT COUNT(*) as count FROM products";
        $result = $conn->query($sql);
        
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>📦 Products in database: " . $row['count'] . "</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Products table query failed: " . $conn->error . "</p>";
        }
        
        // Test users table
        $sql = "SELECT COUNT(*) as count FROM users";
        $result = $conn->query($sql);
        
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>👥 Users in database: " . $row['count'] . "</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Users table query failed: " . $conn->error . "</p>";
        }
        
        // Test orders table
        $sql = "SELECT COUNT(*) as count FROM orders";
        $result = $conn->query($sql);
        
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>📋 Orders in database: " . $row['count'] . "</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Orders table query failed: " . $conn->error . "</p>";
        }
        
        // Check admin user
        $sql = "SELECT username, email FROM users WHERE username = 'TheAdmin'";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<p>👑 Admin user found: " . $row['username'] . " (" . $row['email'] . ")</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Admin user not found</p>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>PHP Info</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>MySQL Extension: " . (extension_loaded('mysqli') ? '✅ Loaded' : '❌ Not Loaded') . "</p>";

echo "<hr>";
echo "<p><a href='medico.html'>← Back to Home</a></p>";
?>
