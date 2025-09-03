<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medico";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "<br>Please ensure XAMPP MySQL is running.");
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Set charset to utf8
$conn->set_charset("utf8");

// Function to safely close connection
function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>
