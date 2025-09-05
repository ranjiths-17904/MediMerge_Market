<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Prevent any output before JSON response
ob_clean();

require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'place_order') {
        try {
            $orderData = json_decode($_POST['orderData'], true);
            $total = floatval($_POST['total']);
            $paymentMethod = $_POST['paymentMethod'] ?? 'cod';
            
            if (!$orderData) {
                throw new Exception('Invalid order data');
            }
            
            // Extract customer information
            $customer = $orderData['customer'];
            $items = $orderData['items'];
            $notes = $orderData['notes'] ?? '';
            $orderId = $orderData['orderId'];
            $userId = $orderData['userId'] ?? null;
            
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
            
            // Prepare customer address
            $fullAddress = $customer['address'] . ', ' . $customer['city'] . ', ' . 
                          $customer['state'] . ' ' . $customer['zipCode'] . ', ' . $customer['country'];
            
            // Insert order into database
            $sql = "INSERT INTO orders (order_id, user_id, customer_name, customer_email, customer_phone, 
                    customer_address, items, total_amount, payment_method, order_status, payment_status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending')";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Failed to prepare statement: ' . $conn->error);
            }
            
            $customerName = $customer['firstName'] . ' ' . $customer['lastName'];
            $itemsJson = json_encode($items);
            
            $stmt->bind_param("sissssds", $orderId, $userId, $customerName, $customer['email'], 
                            $customer['phone'], $fullAddress, $itemsJson, $total, $paymentMethod);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to save order: ' . $stmt->error);
            }
            
            $stmt->close();
            
            // Process payment if not COD
            if ($paymentMethod !== 'cod') {
                $paymentResult = processPayment($orderData, $total, $orderId);
                if (!$paymentResult['success']) {
                    // Update order status to failed
                    $updateSql = "UPDATE orders SET payment_status = 'failed' WHERE order_id = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("s", $orderId);
                    $updateStmt->execute();
                    $updateStmt->close();
                    
                    throw new Exception('Payment failed: ' . $paymentResult['message']);
                }
                
                // Update order with payment success
                $updateSql = "UPDATE orders SET payment_status = 'completed', transaction_id = ? WHERE order_id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("ss", $paymentResult['transaction_id'], $orderId);
                $updateStmt->execute();
                $updateStmt->close();
            }
            
            // Send receipt via SMS
            sendReceiptSMS($customer['phone'], $orderId, $total, $paymentMethod);
            
            // Send notifications
            sendOrderNotifications($conn, $orderId, $userId, 'order_placed', 'Your order has been placed successfully and is being processed.');
            
            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Order placed successfully',
                'orderId' => $orderId,
                'total' => $total,
                'paymentStatus' => $paymentMethod === 'cod' ? 'pending' : 'completed'
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

function processPayment($orderData, $total, $orderId) {
    // Call payment gateway API
    $paymentData = [
        'action' => 'process_payment',
        'amount' => $total,
        'paymentMethod' => $orderData['paymentMethod'],
        'orderId' => $orderId,
        'customer' => $orderData['customer'],
        'cardData' => $orderData['cardData'] ?? [],
        'upiData' => $orderData['upiData'] ?? [],
        'bankData' => $orderData['bankData'] ?? [],
        'walletData' => $orderData['walletData'] ?? []
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/api/payment_gateway.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return [
            'success' => false,
            'message' => 'Payment gateway error'
        ];
    }
    
    $result = json_decode($response, true);
    if (!$result) {
        return [
            'success' => false,
            'message' => 'Invalid payment gateway response'
        ];
    }
    
    return $result;
}

function sendReceiptSMS($phone, $orderId, $total, $paymentMethod) {
    $message = "Thank you for your order! Order ID: $orderId, Amount: ₹$total, Payment: $paymentMethod. Your order will be delivered soon. - MediMerge";
    
    // In production, integrate with actual SMS gateway like Twilio, MSG91, etc.
    // For now, we'll just log the message
    error_log("SMS sent to $phone: $message");
    
    return true;
}

function sendOrderNotifications($conn, $orderId, $userId, $type, $message) {
    $title = '';
    $notificationType = 'info';
    
    switch ($type) {
        case 'order_placed':
            $title = 'New Order Placed';
            $notificationType = 'success';
            break;
        case 'order_processing':
            $title = 'Order Processing';
            $notificationType = 'info';
            break;
        case 'order_shipped':
            $title = 'Order Shipped';
            $notificationType = 'info';
            break;
        case 'order_delivered':
            $title = 'Order Delivered';
            $notificationType = 'success';
            break;
        case 'order_cancelled':
            $title = 'Order Cancelled';
            $notificationType = 'warning';
            break;
    }
    
    // Send notification to user
    $userSql = "INSERT INTO notifications (user_id, user_type, title, message, type, order_id, created_at) 
                VALUES (?, 'user', ?, ?, ?, ?, NOW())";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bind_param("isssi", $userId, $title, $message, $notificationType, $orderId);
    $userStmt->execute();
    $userStmt->close();
    
    // Send notification to admin
    $adminSql = "INSERT INTO notifications (user_id, user_type, title, message, type, order_id, created_at) 
                 VALUES (NULL, 'admin', ?, ?, ?, ?, NOW())";
    $adminStmt = $conn->prepare($adminSql);
    $adminMessage = "Order #{$orderId}: {$message}";
    $adminStmt->bind_param("sssi", $title, $adminMessage, $notificationType, $orderId);
    $adminStmt->execute();
    $adminStmt->close();
}

$conn->close();
?>
