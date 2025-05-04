<?php
session_start();
header('Content-Type: application/json'); // Ensure the response is JSON

if (isset($_SESSION['username'])) {
    echo json_encode(['username' => $_SESSION['username']]);
} else {
    echo json_encode(['username' => null]);
}
?>
