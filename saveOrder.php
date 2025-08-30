<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Prevent any output before JSON response
ob_clean();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medico";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'place_order') {
        try {
            $orderData = json_decode($_POST['orderData'], true);
            $total = floatval($_POST['total']);
            
            if (!$orderData) {
                throw new Exception('Invalid order data');
            }
            
            // Extract customer information
            $customer = $orderData['customer'];
            $items = $orderData['items'];
            $paymentMethod = $orderData['paymentMethod'];
            $notes = $orderData['notes'] ?? '';
            $orderId = $orderData['orderId'];
            
            // Validate required fields
            if (empty($customer['firstName']) || empty($customer['lastName']) || 
                empty($customer['email']) || empty($customer['phone']) || 
                empty($customer['address']) || empty($customer['city']) || 
                empty($customer['state']) || empty($customer['zipCode']) || 
                empty($customer['country'])) {
                throw new Exception('All required customer fields must be filled');
            }
            
            if (empty($items)) {
                throw new Exception('Order must contain at least one item');
            }
            
            if (empty($paymentMethod)) {
                throw new Exception('Payment method must be selected');
            }
            
            // Prepare customer address
            $fullAddress = $customer['address'] . ', ' . $customer['city'] . ', ' . 
                          $customer['state'] . ' ' . $customer['zipCode'] . ', ' . $customer['country'];
            
            // Insert order into database
            $sql = "INSERT INTO orders (order_id, customer_name, customer_email, customer_phone, 
                    customer_address, items, total_amount, payment_method, order_status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Failed to prepare statement: ' . $conn->error);
            }
            
            $customerName = $customer['firstName'] . ' ' . $customer['lastName'];
            $itemsJson = json_encode($items);
            
            $stmt->bind_param("sssssds", $orderId, $customerName, $customer['email'], 
                            $customer['phone'], $fullAddress, $itemsJson, $total, $paymentMethod);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to save order: ' . $stmt->error);
            }
            
            $stmt->close();
            
            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Order placed successfully',
                'orderId' => $orderId,
                'total' => $total
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

$conn->close();
?>
