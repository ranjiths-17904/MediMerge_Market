<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($method) {
    case 'GET':
        // Get cart contents
        $cart_items = [];
        $total = 0;
        
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $stmt = $conn->prepare("SELECT id, name, description, price, category, image, stock FROM products WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $product = $result->fetch_assoc();
                    $product['quantity'] = $quantity;
                    $product['subtotal'] = $product['price'] * $quantity;
                    $cart_items[] = $product;
                    $total += $product['subtotal'];
                }
                
                $stmt->close();
            }
        }
        
        echo json_encode([
            'success' => true,
            'cart' => $cart_items,
            'total' => $total,
            'item_count' => array_sum($_SESSION['cart'])
        ]);
        break;
        
    case 'POST':
        // Add item to cart
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $input = $_POST;
        }
        
        $product_id = intval($input['product_id'] ?? 0);
        $quantity = intval($input['quantity'] ?? 1);
        
        if ($product_id <= 0 || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID or quantity']);
            exit;
        }
        
        // Check if product exists and has stock
        $stmt = $conn->prepare("SELECT id, name, stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            $stmt->close();
            exit;
        }
        
        $product = $result->fetch_assoc();
        $stmt->close();
        
        // Check stock availability
        if ($product['stock'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Insufficient stock. Available: ' . $product['stock']]);
            exit;
        }
        
        // Add to cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Product added to cart',
            'cart_count' => array_sum($_SESSION['cart'])
        ]);
        break;
        
    case 'PUT':
        // Update cart item quantity
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $input = $_POST;
        }
        
        $product_id = intval($input['product_id'] ?? 0);
        $quantity = intval($input['quantity'] ?? 0);
        
        if ($product_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            exit;
        }
        
        if ($quantity <= 0) {
            // Remove item if quantity is 0 or negative
            unset($_SESSION['cart'][$product_id]);
            echo json_encode(['success' => true, 'message' => 'Product removed from cart']);
        } else {
            // Check stock availability
            $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                if ($product['stock'] >= $quantity) {
                    $_SESSION['cart'][$product_id] = $quantity;
                    echo json_encode(['success' => true, 'message' => 'Cart updated']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Insufficient stock. Available: ' . $product['stock']]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
            }
            
            $stmt->close();
        }
        break;
        
    case 'DELETE':
        // Remove item from cart
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $input = $_GET;
        }
        
        $product_id = intval($input['product_id'] ?? 0);
        
        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            echo json_encode(['success' => true, 'message' => 'Product removed from cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found in cart']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

closeConnection($conn);
?>
