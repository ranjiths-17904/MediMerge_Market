<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medico";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepared statement to get user details
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Store user details in session
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;

            // Check specific user credentials for conditional redirection
            if ($username == "Ranjith17" && $email == "ranjithpal17@gmail.com") {
                header('Location: medico3.html'); // Redirect to medico3.html for specific user
                exit;
            } else {
                header('Location: medico1.html'); // Redirect to medico1.html for all other users
                exit;
            }
        } else {
            // Invalid password
            $_SESSION['error'] = "Invalid email or password.";
            header('Location: login.php'); // Redirect back to login page
            exit;
        }
    } else {
        // Invalid email
        $_SESSION['error'] = "Invalid email or password.";
        header('Location: login.php'); // Redirect back to login page
        exit;
    }

    $stmt->close();
}

$conn->close();
?>
