<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT');
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
        // Get all orders
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
        $result = $conn->query($sql);
        
        $orders = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $row['items'] = json_decode($row['items'], true);
                $orders[] = $row;
            }
        }
        
        echo json_encode(['success' => true, 'orders' => $orders]);
        break;
        
    case 'PUT':
        // Update order status
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $input = $_POST;
        }
        
        $order_id = $input['order_id'] ?? '';
        $status = $input['status'] ?? '';
        
        if (empty($order_id) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'Order ID and status are required']);
            exit;
        }
        
        $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $valid_statuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }
        
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $status, $order_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating order status: ' . $stmt->error]);
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
