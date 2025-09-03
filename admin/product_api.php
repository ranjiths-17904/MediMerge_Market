<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Check if user is admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'TheAdmin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied. Admin only.']);
    exit;
}

// Include database configuration
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all products
        $sql = "SELECT * FROM products ORDER BY id DESC";
        $result = $conn->query($sql);
        
        $products = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        echo json_encode(['success' => true, 'products' => $products]);
        break;
        
    case 'POST':
        // Add new product
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $input = $_POST;
        }
        
        $name = $input['name'] ?? '';
        $description = $input['description'] ?? '';
        $price = floatval($input['price'] ?? 0);
        $category = $input['category'] ?? '';
        $image = $input['image'] ?? '';
        $stock = intval($input['stock'] ?? 0);
        
        if (empty($name) || empty($description) || $price <= 0 || empty($category)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required and price must be greater than 0']);
            exit;
        }
        
        $sql = "INSERT INTO products (name, description, price, category, image, stock) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssi", $name, $description, $price, $category, $image, $stock);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product added successfully', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding product: ' . $stmt->error]);
        }
        
        $stmt->close();
        break;
        
    case 'PUT':
        // Update product
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = intval($input['id'] ?? 0);
        $name = $input['name'] ?? '';
        $description = $input['description'] ?? '';
        $price = floatval($input['price'] ?? 0);
        $category = $input['category'] ?? '';
        $image = $input['image'] ?? '';
        $stock = intval($input['stock'] ?? 0);
        
        if ($id <= 0 || empty($name) || empty($description) || $price <= 0 || empty($category)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
            exit;
        }
        
        $sql = "UPDATE products SET name=?, description=?, price=?, category=?, image=?, stock=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssii", $name, $description, $price, $category, $image, $stock, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating product: ' . $stmt->error]);
        }
        
        $stmt->close();
        break;
        
    case 'DELETE':
        // Delete product
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $input = $_GET;
        }
        
        $id = intval($input['id'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            exit;
        }
        
        $sql = "DELETE FROM products WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting product: ' . $stmt->error]);
        }
        
        $stmt->close();
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

closeConnection($conn);
?>
