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
    
    switch ($action) {
        case 'get_notifications':
            $userId = $input['user_id'] ?? null;
            $userType = $input['user_type'] ?? 'user'; // 'user' or 'admin'
            $notifications = getNotifications($conn, $userId, $userType);
            echo json_encode([
                'success' => true,
                'notifications' => $notifications
            ]);
            break;
            
        case 'mark_read':
            $notificationId = $input['notification_id'] ?? null;
            $result = markNotificationAsRead($conn, $notificationId);
            echo json_encode($result);
            break;
            
        case 'mark_all_read':
            $userId = $input['user_id'] ?? null;
            $userType = $input['user_type'] ?? 'user';
            $result = markAllNotificationsAsRead($conn, $userId, $userType);
            echo json_encode($result);
            break;
            
        case 'create_notification':
            $notificationData = $input['notification'] ?? [];
            $result = createNotification($conn, $notificationData);
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

function getNotifications($conn, $userId, $userType) {
    $sql = "SELECT * FROM notifications 
            WHERE (user_id = ? OR (user_type = ? AND user_id IS NULL))
            ORDER BY created_at DESC 
            LIMIT 50";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $userId, $userType);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
        $notifications[] = $row;
    }
    
    $stmt->close();
    return $notifications;
}

function markNotificationAsRead($conn, $notificationId) {
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notificationId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return [
            'success' => true,
            'message' => 'Notification marked as read'
        ];
    } else {
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Failed to mark notification as read'
        ];
    }
}

function markAllNotificationsAsRead($conn, $userId, $userType) {
    $sql = "UPDATE notifications 
            SET is_read = 1 
            WHERE (user_id = ? OR (user_type = ? AND user_id IS NULL)) 
            AND is_read = 0";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $userId, $userType);
    
    if ($stmt->execute()) {
        $stmt->close();
        return [
            'success' => true,
            'message' => 'All notifications marked as read'
        ];
    } else {
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Failed to mark notifications as read'
        ];
    }
}

function createNotification($conn, $notificationData) {
    $userId = $notificationData['user_id'] ?? null;
    $userType = $notificationData['user_type'] ?? 'user';
    $title = $notificationData['title'] ?? '';
    $message = $notificationData['message'] ?? '';
    $type = $notificationData['type'] ?? 'info';
    $orderId = $notificationData['order_id'] ?? null;
    
    $sql = "INSERT INTO notifications (user_id, user_type, title, message, type, order_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssi", $userId, $userType, $title, $message, $type, $orderId);
    
    if ($stmt->execute()) {
        $notificationId = $conn->insert_id;
        $stmt->close();
        return [
            'success' => true,
            'message' => 'Notification created successfully',
            'notification_id' => $notificationId
        ];
    } else {
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Failed to create notification'
        ];
    }
}

// Function to send order notifications
function sendOrderNotification($conn, $orderId, $userId, $type, $message) {
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
    createNotification($conn, [
        'user_id' => $userId,
        'user_type' => 'user',
        'title' => $title,
        'message' => $message,
        'type' => $notificationType,
        'order_id' => $orderId
    ]);
    
    // Send notification to admin
    createNotification($conn, [
        'user_id' => null,
        'user_type' => 'admin',
        'title' => $title,
        'message' => "Order #{$orderId}: {$message}",
        'type' => $notificationType,
        'order_id' => $orderId
    ]);
}

$conn->close();
?>
