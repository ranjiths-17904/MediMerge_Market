<?php
session_start();

// Check if the user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || $_SESSION['username'] !== 'TheAdmin') {
    header('Location: ../login.php');
    exit();
}

// Include database configuration
require_once '../config/database.php';

// Handle product operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_product':
                $name = $conn->real_escape_string($_POST['name']);
                $description = $conn->real_escape_string($_POST['description']);
                $price = floatval($_POST['price']);
                $category = $conn->real_escape_string($_POST['category']);
                $image = $conn->real_escape_string($_POST['image']);
                $stock = intval($_POST['stock']);
                
                $sql = "INSERT INTO products (name, description, price, category, image, stock) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdssi", $name, $description, $price, $category, $image, $stock);
                
                if ($stmt->execute()) {
                    $success_message = "Product added successfully!";
                } else {
                    $error_message = "Error adding product: " . $stmt->error;
                }
                $stmt->close();
                break;
                
            case 'update_product':
                $id = intval($_POST['id']);
                $name = $conn->real_escape_string($_POST['name']);
                $description = $conn->real_escape_string($_POST['description']);
                $price = floatval($_POST['price']);
                $category = $conn->real_escape_string($_POST['category']);
                $image = $conn->real_escape_string($_POST['image']);
                $stock = intval($_POST['stock']);
                
                $sql = "UPDATE products SET name=?, description=?, price=?, category=?, image=?, stock=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdssii", $name, $description, $price, $category, $image, $stock, $id);
                
                if ($stmt->execute()) {
                    $success_message = "Product updated successfully!";
                } else {
                    $error_message = "Error updating product: " . $stmt->error;
                }
                $stmt->close();
                break;
                
            case 'delete_product':
                $id = intval($_POST['id']);
                
                $sql = "DELETE FROM products WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    $success_message = "Product deleted successfully!";
                } else {
                    $error_message = "Error deleting product: " . $stmt->error;
                }
                $stmt->close();
                break;

            case 'update_order_status':
                $orderId = $conn->real_escape_string($_POST['order_id']);
                $status = $conn->real_escape_string($_POST['status']);
                
                // Get order details for notification
                $orderSql = "SELECT user_id, total_amount FROM orders WHERE order_id = ?";
                $orderStmt = $conn->prepare($orderSql);
                $orderStmt->bind_param("s", $orderId);
                $orderStmt->execute();
                $orderResult = $orderStmt->get_result();
                $order = $orderResult->fetch_assoc();
                $orderStmt->close();
                
                $sql = "UPDATE orders SET order_status=? WHERE order_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $status, $orderId);
                
                if ($stmt->execute()) {
                    $success_message = "Order status updated successfully!";
                    
                    // Send notification to user
                    $message = '';
                    $type = '';
                    switch ($status) {
                        case 'processing':
                            $message = 'Your order is being processed and will be shipped soon.';
                            $type = 'order_processing';
                            break;
                        case 'shipped':
                            $message = 'Your order has been shipped and is on its way to you.';
                            $type = 'order_shipped';
                            break;
                        case 'delivered':
                            $message = 'Your order has been delivered successfully. Thank you for shopping with us!';
                            $type = 'order_delivered';
                            break;
                        case 'cancelled':
                            $message = 'Your order has been cancelled. If you have any questions, please contact support.';
                            $type = 'order_cancelled';
                            break;
                    }
                    
                    if ($message && $order['user_id']) {
                        sendOrderNotifications($conn, $orderId, $order['user_id'], $type, $message);
                    }
                } else {
                    $error_message = "Error updating order status: " . $stmt->error;
                }
                $stmt->close();
                break;
        }
    }
}

// Fetch statistics
$stats_sql = "SELECT 
    COUNT(*) as total_products,
    SUM(stock) as total_stock,
    COUNT(DISTINCT category) as total_categories
FROM products";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

$orders_sql = "SELECT 
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue,
    COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending_orders,
    COUNT(CASE WHEN order_status = 'delivered' THEN 1 END) as delivered_orders
FROM orders";
$orders_result = $conn->query($orders_sql);
$orders_stats = $orders_result->fetch_assoc();

$users_sql = "SELECT COUNT(*) as total_users FROM users WHERE is_admin = FALSE";
$users_result = $conn->query($users_sql);
$users_stats = $users_result->fetch_assoc();

// Fetch all products
$products_sql = "SELECT * FROM products ORDER BY id DESC";
$products_result = $conn->query($products_sql);

// Fetch recent orders
$recent_orders_sql = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 10";
$recent_orders_result = $conn->query($recent_orders_sql);

// Fetch all users
$users_list_sql = "SELECT id, username, email, created_at FROM users WHERE is_admin = FALSE ORDER BY created_at DESC";
$users_list_result = $conn->query($users_list_sql);

closeConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MediMerge</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Style/responsive.css">
    <style>
        .admin-dashboard {
            background: #f8fafc;
            min-height: 100vh;
            padding-top: 80px;
        }

        .admin-header {
            background: linear-gradient(135deg, #11b671, #0ea55d);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .admin-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .admin-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .stat-card h3 {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .stat-card .stat-change {
            font-size: 0.875rem;
            color: #10b981;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .content-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .content-card h3 {
            padding: 1.5rem;
            margin: 0;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            font-weight: 600;
            color: #1e293b;
        }

        .content-card .card-body {
            padding: 1.5rem;
        }

        .product-item, .order-item, .user-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .product-item:last-child, .order-item:last-child, .user-item:last-child {
            border-bottom: none;
        }

        .product-info, .order-info, .user-info {
            flex: 1;
        }

        .product-name, .order-id, .user-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .product-category, .order-status, .user-email {
            font-size: 0.875rem;
            color: #64748b;
        }

        .product-price {
            font-weight: 600;
            color: #10b981;
        }

        .order-amount {
            font-weight: 600;
            color: #1e293b;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit, .btn-delete, .btn-view {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-edit {
            background: #3b82f6;
            color: white;
        }

        .btn-edit:hover {
            background: #2563eb;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .btn-view {
            background: #10b981;
            color: white;
        }

        .btn-view:hover {
            background: #059669;
        }

        .add-product-form {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #11b671;
        }

        .btn-primary {
            background: linear-gradient(135deg, #11b671, #0ea55d);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(17, 182, 113, 0.3);
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-shipped {
            background: #d1fae5;
            color: #065f46;
        }

        .status-delivered {
            background: #dcfce7;
            color: #166534;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-header h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-edit, .btn-delete, .btn-view {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="flex justify-between items-center">
                <a href="#" class="navbar-brand">MediMerge Admin</a>
                <div class="flex items-center gap-4">
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="../logout.php" class="btn btn-outline">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="admin-dashboard">
        <div class="admin-header">
            <div class="container">
                <h1>Admin Dashboard</h1>
                <p>Manage your MediMerge store efficiently</p>
            </div>
        </div>

        <div class="container">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <div class="stat-value"><?php echo $stats['total_products']; ?></div>
                    <div class="stat-change">+12% from last month</div>
                </div>
                <div class="stat-card">
                    <h3>Total Stock</h3>
                    <div class="stat-value"><?php echo $stats['total_stock']; ?></div>
                    <div class="stat-change">+5% from last month</div>
                </div>
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <div class="stat-value"><?php echo $orders_stats['total_orders']; ?></div>
                    <div class="stat-change">+18% from last month</div>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="stat-value">₹<?php echo number_format($orders_stats['total_revenue'], 2); ?></div>
                    <div class="stat-change">+25% from last month</div>
                </div>
                <div class="stat-card">
                    <h3>Pending Orders</h3>
                    <div class="stat-value"><?php echo $orders_stats['pending_orders']; ?></div>
                    <div class="stat-change">Requires attention</div>
                </div>
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="stat-value"><?php echo $users_stats['total_users']; ?></div>
                    <div class="stat-change">+8% from last month</div>
                </div>
            </div>

            <!-- Add Product Form -->
            <div class="add-product-form">
                <h3 style="margin-bottom: 1.5rem;">Add New Product</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_product">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-input" required>
                                <option value="">Select Category</option>
                                <option value="Pain Relief">Pain Relief</option>
                                <option value="Cold & Cough">Cold & Cough</option>
                                <option value="Vitamins">Vitamins</option>
                                <option value="Diabetes">Diabetes</option>
                                <option value="First Aid">First Aid</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Price (₹)</label>
                            <input type="number" name="price" class="form-input" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock" class="form-input" min="0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Image URL</label>
                            <input type="url" name="image" class="form-input" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-input" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Add Product</button>
                </form>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Products -->
                <div class="content-card">
                    <h3>Recent Products</h3>
                    <div class="card-body">
                        <?php if ($products_result->num_rows > 0): ?>
                            <?php while ($product = $products_result->fetch_assoc()): ?>
                                <div class="product-item">
                                    <div class="product-info">
                                        <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                        <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                                    </div>
                                    <div class="product-price">₹<?php echo number_format($product['price'], 2); ?></div>
                                    <div class="action-buttons">
                                        <button class="btn-edit" onclick="editProduct(<?php echo $product['id']; ?>)">Edit</button>
                                        <button class="btn-delete" onclick="deleteProduct(<?php echo $product['id']; ?>)">Delete</button>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-gray-500">No products found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Orders -->
                <div class="content-card">
                    <h3>Recent Orders</h3>
                    <div class="card-body">
                        <?php if ($recent_orders_result->num_rows > 0): ?>
                            <?php while ($order = $recent_orders_result->fetch_assoc()): ?>
                                <div class="order-item">
                                    <div class="order-info">
                                        <div class="order-id">Order #<?php echo htmlspecialchars($order['order_id']); ?></div>
                                        <div class="order-status">
                                            <span class="status-badge status-<?php echo strtolower($order['order_status']); ?>">
                                                <?php echo ucfirst($order['order_status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="order-amount">₹<?php echo number_format($order['total_amount'], 2); ?></div>
                                    <div class="action-buttons">
                                        <button class="btn-view" onclick="viewOrder('<?php echo $order['order_id']; ?>')">View</button>
                                        <select onchange="updateOrderStatus('<?php echo $order['order_id']; ?>', this.value)" class="form-input" style="width: auto; padding: 0.25rem;">
                                            <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo $order['order_status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="shipped" <?php echo $order['order_status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $order['order_status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-gray-500">No orders found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Users -->
            <div class="content-card">
                <h3>Registered Users</h3>
                <div class="card-body">
                    <?php if ($users_list_result->num_rows > 0): ?>
                        <?php while ($user = $users_list_result->fetch_assoc()): ?>
                            <div class="user-item">
                                <div class="user-info">
                                    <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                                <div class="text-gray-500"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-gray-500">No users found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editProduct(productId) {
            const name = prompt('New name (leave blank to keep)');
            const price = prompt('New price (leave blank to keep)');
            const category = prompt('New category (leave blank to keep)');
            const image = prompt('New image URL (leave blank to keep)');
            const stock = prompt('New stock (leave blank to keep)');
            const description = prompt('New description (leave blank to keep)');
            if(name || price || category || image || stock || description){
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_product">
                    <input type="hidden" name="id" value="${productId}">
                    <input type="hidden" name="name" value="${name||''}">
                    <input type="hidden" name="price" value="${price||''}">
                    <input type="hidden" name="category" value="${category||''}">
                    <input type="hidden" name="image" value="${image||''}">
                    <input type="hidden" name="stock" value="${stock||''}">
                    <input type="hidden" name="description" value="${description||''}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_product">
                    <input type="hidden" name="id" value="${productId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function viewOrder(orderId) {
            window.location.href = `../confirmation.html?order_id=${orderId}`;
        }

        function updateOrderStatus(orderId, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="update_order_status">
                <input type="hidden" name="order_id" value="${orderId}">
                <input type="hidden" name="status" value="${status}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        // Initialize notification component for admin
        document.addEventListener('DOMContentLoaded', function() {
            if (window.notificationComponent) {
                window.notificationComponent.setUserType('admin');
            }
        });
    </script>
    <script src="../notification-component.js"></script>
</body>
</html>

<?php
function sendOrderNotifications($conn, $orderId, $userId, $type, $message) {
    $title = '';
    $notificationType = 'info';
    
    switch ($type) {
        case 'order_placed':
            $title = 'New Order Placed';
            $notificationType = 'success';
            break;
        case 'order_processing':
            $title = 'Order Processing';
            $notificationType = 'info';
            break;
        case 'order_shipped':
            $title = 'Order Shipped';
            $notificationType = 'info';
            break;
        case 'order_delivered':
            $title = 'Order Delivered';
            $notificationType = 'success';
            break;
        case 'order_cancelled':
            $title = 'Order Cancelled';
            $notificationType = 'warning';
            break;
    }
    
    // Send notification to user
    $userSql = "INSERT INTO notifications (user_id, user_type, title, message, type, order_id, created_at) 
                VALUES (?, 'user', ?, ?, ?, ?, NOW())";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bind_param("isssi", $userId, $title, $message, $notificationType, $orderId);
    $userStmt->execute();
    $userStmt->close();
    
    // Send notification to admin
    $adminSql = "INSERT INTO notifications (user_id, user_type, title, message, type, order_id, created_at) 
                 VALUES (NULL, 'admin', ?, ?, ?, ?, NOW())";
    $adminStmt = $conn->prepare($adminSql);
    $adminMessage = "Order #{$orderId}: {$message}";
    $adminStmt->bind_param("sssi", $title, $adminMessage, $notificationType, $orderId);
    $adminStmt->execute();
    $adminStmt->close();
}
?>
