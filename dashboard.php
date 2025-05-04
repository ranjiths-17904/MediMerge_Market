<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

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

// Handle delete operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']); // Sanitize input

    // Prevent deleting the currently logged-in user
    if ($delete_id === $_SESSION['user_id']) {
        $error_message = "You cannot delete your own account.";
    } else {
        $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete_stmt->bind_param("i", $delete_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        $success_message = "User deleted successfully.";
    }
}

// Fetch all users' details from the database
$sql = "SELECT id, username, email FROM users";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f3f6;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 10px;
            overflow: hidden;
        }

        h1 {
            color: #4CAF50;
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .message, .error {
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }

        .message {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
            font-size: 16px;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
            padding: 6px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .home-btn {
            display: inline-block;
            margin-left: 2rem;
            padding: 12px 30px;
            background-color: #4CAF50;
            color: #fff;
            border-radius: 30px;
            text-decoration: none;
            font-size: 18px;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .home-btn:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .home-btn:active {
            background-color: #397d3b;
            transform: scale(0.98);
        }
    </style>
</head>
<body>
    <a href="medico3.html">
        <button class="home-btn">🏠 Home</button>
    </a>

    <div class="container">
        <h1>Users Table</h1>

        <?php if (isset($success_message)) : ?>
            <div class="message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)) : ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <!-- Delete form -->
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display: inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
