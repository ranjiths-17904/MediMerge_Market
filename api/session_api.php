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
        case 'get_current_user':
            $user = getCurrentUser();
            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
            break;
            
        case 'set_user_id':
            $userId = $input['user_id'] ?? null;
            if ($userId) {
                // Set user ID in session
                session_start();
                $_SESSION['user_id'] = $userId;
                echo json_encode([
                    'success' => true,
                    'message' => 'User ID set successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User ID is required'
                ]);
            }
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

function getCurrentUser() {
    session_start();
    
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        
        // Get user details from database
        global $conn;
        $sql = "SELECT id, username, email, phone, address, city, state, zip_code, country, is_admin FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $stmt->close();
            return $user;
        }
        $stmt->close();
    }
    
    return null;
}

$conn->close();
?>
