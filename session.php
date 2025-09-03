<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$response = array();

if (isset($_SESSION['username'])) {
    $response['username'] = $_SESSION['username'];
    $response['email'] = $_SESSION['email'] ?? null;
    $response['isAdmin'] = ($_SESSION['username'] === 'TheAdmin');
} else {
    $response['username'] = null;
    $response['email'] = null;
    $response['isAdmin'] = false;
}

echo json_encode($response);
?>
