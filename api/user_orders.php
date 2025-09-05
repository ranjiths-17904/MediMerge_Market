<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $action = $input['action'] ?? '';
    $userId = $input['user_id'] ?? null;
    
    if (empty($userId)) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID is required'
        ]);
        exit;
    }
    
    switch ($action) {
        case 'get_user_orders':
            $orders = getUserOrders($conn, $userId);
            echo json_encode([
                'success' => true,
                'orders' => $orders
            ]);
            break;
            
        case 'get_order_details':
            $orderId = $input['order_id'] ?? '';
            if (empty($orderId)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Order ID is required'
                ]);
                exit;
            }
            
            $orderDetails = getOrderDetails($conn, $orderId, $userId);
            if ($orderDetails) {
                echo json_encode([
                    'success' => true,
                    'order' => $orderDetails
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Order not found'
                ]);
            }
            break;
            
        case 'cancel_order':
            $orderId = $input['order_id'] ?? '';
            if (empty($orderId)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Order ID is required'
                ]);
                exit;
            }
            
            $result = cancelOrder($conn, $orderId, $userId);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            break;
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests are allowed'
    ]);
}

function getUserOrders($conn, $userId) {
    $sql = "SELECT o.*, 
            COUNT(JSON_EXTRACT(o.items, '$[*]')) as item_count
            FROM orders o 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        // Parse items JSON to get item count
        $items = json_decode($row['items'], true);
        $row['item_count'] = is_array($items) ? count($items) : 0;
        
        // Format dates
        $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
        $row['updated_at'] = date('Y-m-d H:i:s', strtotime($row['updated_at']));
        
        // Calculate estimated delivery time
        $row['estimated_delivery'] = calculateEstimatedDelivery($row['order_status'], $row['created_at']);
        
        $orders[] = $row;
    }
    
    $stmt->close();
    return $orders;
}

function calculateEstimatedDelivery($orderStatus, $createdAt) {
    $createdTime = strtotime($createdAt);
    $currentTime = time();
    
    switch ($orderStatus) {
        case 'pending':
            return date('Y-m-d H:i:s', $createdTime + (2 * 24 * 60 * 60)); // 2 days
        case 'processing':
            return date('Y-m-d H:i:s', $createdTime + (1 * 24 * 60 * 60)); // 1 day
        case 'shipped':
            return date('Y-m-d H:i:s', $createdTime + (0.5 * 24 * 60 * 60)); // 12 hours
        case 'delivered':
            return 'Delivered';
        case 'cancelled':
            return 'Cancelled';
        default:
            return date('Y-m-d H:i:s', $createdTime + (2 * 24 * 60 * 60)); // Default 2 days
    }
}

function getOrderDetails($conn, $orderId, $userId) {
    $sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $orderId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return null;
    }
    
    $order = $result->fetch_assoc();
    
    // Parse items JSON
    $order['items'] = json_decode($order['items'], true);
    
    // Format dates
    $order['created_at'] = date('Y-m-d H:i:s', strtotime($order['created_at']));
    $order['updated_at'] = date('Y-m-d H:i:s', strtotime($order['updated_at']));
    
    $stmt->close();
    return $order;
}

function cancelOrder($conn, $orderId, $userId) {
    // Check if order exists and belongs to user
    $checkSql = "SELECT order_status FROM orders WHERE order_id = ? AND user_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("si", $orderId, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        $checkStmt->close();
        return [
            'success' => false,
            'message' => 'Order not found'
        ];
    }
    
    $order = $checkResult->fetch_assoc();
    $checkStmt->close();
    
    // Check if order can be cancelled
    if ($order['order_status'] !== 'pending' && $order['order_status'] !== 'processing') {
        return [
            'success' => false,
            'message' => 'Order cannot be cancelled at this stage'
        ];
    }
    
    // Update order status to cancelled
    $updateSql = "UPDATE orders SET order_status = 'cancelled', updated_at = CURRENT_TIMESTAMP WHERE order_id = ? AND user_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $orderId, $userId);
    
    if ($updateStmt->execute()) {
        $updateStmt->close();
        return [
            'success' => true,
            'message' => 'Order cancelled successfully'
        ];
    } else {
        $updateStmt->close();
        return [
            'success' => false,
            'message' => 'Failed to cancel order'
        ];
    }
}

$conn->close();
?>
