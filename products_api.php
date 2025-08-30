<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medico";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, name, description, price, category, image, stock FROM products ORDER BY id DESC";
$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['price'] = (float)$row['price'];
        $row['stock'] = (int)$row['stock'];
        $row['id'] = (int)$row['id'];
        $products[] = $row;
    }
}

echo json_encode($products);
$conn->close();
?>


