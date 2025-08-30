<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
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
        }
    }
}

// Fetch all products
$products_sql = "SELECT * FROM products ORDER BY id DESC";
$products_result = $conn->query($products_sql);

// Fetch all users
$users_sql = "SELECT id, username, email FROM users";
$users_result = $conn->query($users_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MediMerge</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #333;
            font-size: 32px;
            font-weight: 700;
        }

        .header h1 span {
            color: #11b671;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #11b671, #0ea55d);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ff4757, #ff3742);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 71, 87, 0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-card h3 {
            color: #666;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
        }

        .stat-card .icon {
            font-size: 24px;
            color: #11b671;
            margin-bottom: 10px;
        }

        .tabs {
            display: flex;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 5px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .tab-btn {
            flex: 1;
            padding: 15px 20px;
            border: none;
            background: transparent;
            color: #666;
            font-weight: 600;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #11b671, #0ea55d);
            color: white;
        }

        .tab-content {
            display: none;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .tab-content.active {
            display: block;
        }

        .add-product-btn {
            background: linear-gradient(135deg, #11b671, #0ea55d);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .add-product-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(17, 182, 113, 0.3);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #333;
            font-size: 24px;
            font-weight: 700;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: #ff4757;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #11b671;
            box-shadow: 0 0 0 3px rgba(17, 182, 113, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #11b671, #0ea55d);
            color: white;
        }

        .btn-secondary {
            background: #f1f1f1;
            color: #666;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff4757, #ff3742);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e1e1e1;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            object-fit: cover;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: #ffc107;
            color: #333;
        }

        .delete-btn {
            background: #ff4757;
            color: white;
        }

        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 10px;
            }

            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .tabs {
                flex-direction: column;
            }

            .modal-content {
                width: 95%;
                padding: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Admin <span>Dashboard</span></h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <div>
                    <div style="font-weight: 600; color: #333;"><?php echo $_SESSION['username']; ?></div>
                    <div style="font-size: 12px; color: #666;">Administrator</div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <h3>Total Users</h3>
                <div class="value"><?php echo $users_result->num_rows; ?></div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-pills"></i></div>
                <h3>Total Products</h3>
                <div class="value"><?php echo $products_result->num_rows; ?></div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                <h3>Total Orders</h3>
                <div class="value">0</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                <h3>Total Revenue</h3>
                <div class="value">₹0</div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" data-tab="products">Products Management</button>
            <button class="tab-btn" data-tab="users">Users Management</button>
        </div>

        <!-- Products Tab -->
        <div class="tab-content active" id="products-tab">
            <button class="add-product-btn" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Product
            </button>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($products_result->num_rows > 0): ?>
                            <?php while($row = $products_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="product-image">
                                    </td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo substr($row['description'], 0, 50) . '...'; ?></td>
                                    <td>₹<?php echo $row['price']; ?></td>
                                    <td><?php echo $row['category']; ?></td>
                                    <td><?php echo $row['stock']; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit-btn" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo $row['name']; ?>', '<?php echo $row['description']; ?>', <?php echo $row['price']; ?>, '<?php echo $row['category']; ?>', '<?php echo $row['image']; ?>', <?php echo $row['stock']; ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="action-btn delete-btn" onclick="deleteProduct(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #666;">
                                    No products found. Add your first product!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Users Tab -->
        <div class="tab-content" id="users-tab">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users_result->num_rows > 0): ?>
                            <?php while($row = $users_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 40px; color: #666;">
                                    No users found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Product</h2>
                <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_product">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Price (₹)</label>
                    <input type="number" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="Pain Relief">Pain Relief</option>
                        <option value="Fever">Fever</option>
                        <option value="Cold & Cough">Cold & Cough</option>
                        <option value="Diabetes">Diabetes</option>
                        <option value="Vitamins">Vitamins</option>
                        <option value="First Aid">First Aid</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image" required>
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Product</h2>
                <button class="close-btn" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_product">
                <input type="hidden" name="id" id="edit-id">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" id="edit-name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit-description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Price (₹)</label>
                    <input type="number" name="price" id="edit-price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="edit-category" required>
                        <option value="">Select Category</option>
                        <option value="Pain Relief">Pain Relief</option>
                        <option value="Fever">Fever</option>
                        <option value="Cold & Cough">Cold & Cough</option>
                        <option value="Diabetes">Diabetes</option>
                        <option value="Vitamins">Vitamins</option>
                        <option value="First Aid">First Aid</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image" id="edit-image" required>
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" id="edit-stock" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab functionality
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabName = btn.dataset.tab;
                
                // Update active tab button
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Show active tab content
                tabContents.forEach(content => {
                    content.classList.remove('active');
                    if (content.id === `${tabName}-tab`) {
                        content.classList.add('active');
                    }
                });
            });
        });

        // Modal functionality
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function openEditModal(id, name, description, price, category, image, stock) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-price').value = price;
            document.getElementById('edit-category').value = category;
            document.getElementById('edit-image').value = image;
            document.getElementById('edit-stock').value = stock;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function deleteProduct(id) {
            // custom confirmation modal
            const overlay=document.createElement('div');
            overlay.className='modal';
            overlay.style.display='block';
            overlay.innerHTML=`
                <div class="modal-content">
                  <div class="modal-header"><h2>Delete Product</h2><button class="close-btn" id="delClose">&times;</button></div>
                  <p>Are you sure you want to delete this product?</p>
                  <div class="form-actions">
                    <button class="btn btn-secondary" id="delCancel">Cancel</button>
                    <button class="btn btn-danger" id="delConfirm">Delete</button>
                  </div>
                </div>`;
            document.body.appendChild(overlay);
            overlay.querySelector('#delClose').onclick=()=>document.body.removeChild(overlay);
            overlay.querySelector('#delCancel').onclick=(e)=>{e.preventDefault();document.body.removeChild(overlay);} ;
            overlay.querySelector('#delConfirm').onclick=(e)=>{
                e.preventDefault();
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_product">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            };
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
