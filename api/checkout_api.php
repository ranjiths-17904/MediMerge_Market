<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }
    
    // Validate required fields
    $required_fields = ['firstName', 'lastName', 'email', 'phone', 'address', 'city', 'state', 'zipCode', 'country'];
    foreach ($required_fields as $field) {
        if (empty($input['customer'][$field])) {
            echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
            exit;
        }
    }
    
    // Check if cart is not empty
    if (empty($_SESSION['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit;
    }
    
    // Validate cart items and calculate total
    $cart_items = [];
    $total_amount = 0;
    
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $conn->prepare("SELECT id, name, price, stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            $stmt->close();
            exit;
        }
        
        $product = $result->fetch_assoc();
        
        // Check stock availability
        if ($product['stock'] < $quantity) {
            echo json_encode(['success' => false, 'message' => "Insufficient stock for {$product['name']}. Available: {$product['stock']}"]);
            $stmt->close();
            exit;
        }
        
        $subtotal = $product['price'] * $quantity;
        $total_amount += $subtotal;
        
        $cart_items[] = [
            'product_id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
        
        $stmt->close();
    }
    
    // Generate unique order ID
    $order_id = 'MM' . date('YmdHis') . rand(1000, 9999);
    
    // Prepare customer data
    $customer_name = $input['customer']['firstName'] . ' ' . $input['customer']['lastName'];
    $customer_email = $input['customer']['email'];
    $customer_phone = $input['customer']['phone'];
    $customer_address = $input['customer']['address'] . ', ' . $input['customer']['city'] . ', ' . $input['customer']['state'] . ' ' . $input['customer']['zipCode'] . ', ' . $input['customer']['country'];
    $payment_method = $input['paymentMethod'] ?? 'Cash on Delivery';
    $fulfillment = $input['fulfillment'] ?? 'delivery';

    // Delivery fee logic: ₹15 per item if delivery
    $itemCount = 0;
    foreach ($cart_items as $ci) { $itemCount += intval($ci['quantity']); }
    $delivery_fee = ($fulfillment === 'delivery') ? (15 * $itemCount) : 0;
    $total_amount = $total_amount + $delivery_fee;
    
    // Insert order into database
    $stmt = $conn->prepare("INSERT INTO orders (order_id, customer_name, customer_email, customer_phone, customer_address, items, total_amount, payment_method, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    
    $items_json = json_encode($cart_items);
    $stmt->bind_param("ssssssds", $order_id, $customer_name, $customer_email, $customer_phone, $customer_address, $items_json, $total_amount, $payment_method);
    
    if ($stmt->execute()) {
        // Update product stock
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $update_stmt->bind_param("ii", $quantity, $product_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully',
            'order_id' => $order_id,
            'total_amount' => $total_amount,
            'delivery_fee' => $delivery_fee,
            'fulfillment' => $fulfillment
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error placing order: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
