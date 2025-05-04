<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Page</title>
</head>
<body>
    <nav>
        <div class="logo">MySite</div>
        <div class="nav-user">
            <span>👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <h1>Welcome to the Product Page, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
</body>
</html>
