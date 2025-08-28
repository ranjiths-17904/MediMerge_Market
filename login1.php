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

            // Admin redirect by credentials
            if ($username === 'TheAdmin') {
                header('Location: dashboard.php');
                exit;
            }

            // Post-login redirect support
            $redirect = isset($_SESSION['post_login_redirect']) ? $_SESSION['post_login_redirect'] : null;
            if ($redirect) { unset($_SESSION['post_login_redirect']); header("Location: $redirect&login=success"); exit; }
            header('Location: medico.html?login=success');
            exit;
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
