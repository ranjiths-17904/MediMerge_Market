<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Check if user is admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'TheAdmin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied. Admin only.']);
    exit;
}

// Include database configuration
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get all users
    $sql = "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $users = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'users' => $users]);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

closeConnection($conn);
?>
