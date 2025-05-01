<?php
session_start();

// Hardcoded admin credentials
$admin_user = "admin";
$admin_pass = "admin123";

// Admin login check
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['admin_username'] == $admin_user && $_POST['admin_password'] == $admin_pass) {
        $_SESSION['admin'] = $admin_user;
    } else {
        die("Wrong credentials! <a href='admin_login.php'>Try again</a>");
    }
}

// Session validation
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Connect to database to get stats
$conn = new mysqli("localhost", "root", "", "ecommerce");

// Get product count
$product_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];

// Get categories count
$categories_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];

// Get customer count
$customer_count = $conn->query("SELECT COUNT(*) as count FROM customers")->fetch_assoc()['count'];

// Get total inventory value
$revenue = 0;
$revenue_query = $conn->query("SELECT SUM(price) as total_value FROM products");
if ($revenue_query && $row = $revenue_query->fetch_assoc()) {
    $revenue = $row['total_value'] ?: 0;
}

// Get total stock and inventory revenue
$total_stock = 0;
$total_inventory_revenue = 0;
$stock_query = $conn->query("SELECT quantity, price FROM products");
if ($stock_query) {
    while ($row = $stock_query->fetch_assoc()) {
        $total_stock += $row['quantity'];
        $total_inventory_revenue += $row['quantity'] * $row['price'];
    }
}

// Get recent products
$recent_products = $conn->query("SELECT products.*, categories.name AS category_name 
                                FROM products 
                                LEFT JOIN categories ON products.category_id = categories.id
                                ORDER BY products.created_at DESC LIMIT 5");

// Get customers
$recent_customers = $conn->query("SELECT * FROM customers ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | E-Commerce</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f07cab;
            --primary-dark: #e26a98;
            --secondary: #5a2d82;
            --secondary-dark: #4a2569;
            --accent: #6366f1;
            --accent-dark: #4f46e5;
            --danger: #f43f5e;
            --danger-dark: #e11d48;
            --success: #10b981;
            --success-dark: #059669;
            --warning: #f59e0b;
            --warning-dark: #d97706;
            --light-bg: #fff6f9;
            --border-color: #f8d7e5;
            --text-dark: #333;
            --text-light: #666;
            --card-shadow: 0 10px 25px rgba(244, 114, 182, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        body {
            background: #f9f5f7;
            background-image: linear-gradient(135deg, #f9f5f7 0%, #fff6f9 100%);
            min-height: 100vh;
            color: var(--text-dark);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Admin navigation */
        .admin-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            padding: 30px 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        .admin-logo {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--secondary);
            text-align: center;
            margin-bottom: 40px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-logo i {
            font-size: 24px;
            margin-right: 10px;
            color: var(--primary);
        }

        .nav-items {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: var(--transition);
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(240, 124, 171, 0.08);
            border-left-color: var(--primary);
            color: var(--primary);
        }

        .nav-item i {
            margin-right: 12px;
            font-size: 18px;
        }

        .logout-wrap {
            margin-top: auto;
            padding: 20px;
            border-top: 1px solid var(--border-color);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff1f5;
            color: var(--danger);
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background: var(--danger);
            color: white;
        }

        .logout-btn i {
            margin-right: 8px;
        }

        /* Main Content */
        .admin-main {
            margin-left: 250px;
            padding: 30px;
        }

        .welcome-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome-text h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 600;
            color: var(--secondary);
            margin: 0 0 10px 0;
        }

        .welcome-text p {
            color: var(--text-light);
            margin: 0;
        }

        .last-login {
            color: var(--text-light);
            font-size: 14px;
            text-align: right;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            z-index: 1;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(240, 124, 171, 0.05), transparent);
            z-index: -1;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .stat-icon {
            margin-bottom: 15px;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
        }

        .stat-products { background: linear-gradient(135deg, #f07cab, #e26a98); }
        .stat-categories { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .stat-customers { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-revenue { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-stock { background: linear-gradient(135deg, #4caf50, #388e3c); }
        .stat-inventory-revenue { background: linear-gradient(135deg, #ff9800, #f57c00); }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 14px;
            font-weight: 500;
        }

        /* Dashboard panels */
        .dashboard-panels {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .panel {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--card-shadow);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .panel-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--secondary);
            margin: 0;
        }

        .panel-action {
            font-size: 14px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .panel-action i {
            margin-left: 5px;
        }

        /* Recent products table */
        .recent-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .recent-table th {
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
            color: var(--text-light);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .recent-table td {
            padding: 12px 15px;
            border-top: 1px solid #f5f5f5;
            vertical-align: middle;
        }

        .recent-table tr:hover td {
            background-color: #fafafa;
        }

        .product-mini {
            display: flex;
            align-items: center;
        }

        .product-mini img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        .no-mini-image {
            width: 40px;
            height: 40px;
            background: #f9f5f9;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 10px;
            margin-right: 15px;
        }

        .product-mini-name {
            font-weight: 500;
            color: var(--text-dark);
            margin: 0;
        }

        /* Recent customers */
        .customer-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .customer-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 10px;
            transition: var(--transition);
        }

        .customer-item:hover {
            background: #fafafa;
        }

        .customer-avatar {
            width: 45px;
            height: 45px;
            background: var(--light-bg);
            border-radius: 50%;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--primary);
            font-weight: 600;
        }

        .customer-info {
            flex: 1;
        }

        .customer-name {
            font-weight: 600;
            margin: 0 0 5px 0;
        }

        .customer-email {
            color: var(--text-light);
            font-size: 13px;
            margin: 0;
        }

        .customer-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            min-width: 80px;
        }

        .status-active {
            background: #e6f7ef;
            color: var(--success);
        }

        .status-blocked {
            background: #fee2e7;
            color: var(--danger);
        }

        /* Quick action buttons */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .action-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            transition: var(--transition);
            text-align: center;
            color: var(--text-dark);
            text-decoration: none;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .action-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .action-add { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
        .action-view { background: linear-gradient(135deg, var(--accent), var(--accent-dark)); }
        .action-categories { background: linear-gradient(135deg, var(--warning), var(--warning-dark)); }
        .action-customers { background: linear-gradient(135deg, var(--success), var(--success-dark)); }

        .action-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .action-desc {
            color: var(--text-light);
            font-size: 13px;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid, .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-panels {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 991px) {
            .admin-nav {
                width: 80px;
                padding: 20px 0;
            }
            
            .admin-logo {
                font-size: 0;
                justify-content: center;
                margin-bottom: 30px;
            }
            
            .admin-logo i {
                margin-right: 0;
                font-size: 28px;
            }
            
            .nav-item {
                justify-content: center;
                padding: 15px;
            }
            
            .nav-item i {
                margin-right: 0;
                font-size: 20px;
            }
            
            .nav-item span {
                display: none;
            }
            
            .admin-main {
                margin-left: 80px;
            }
            
            .logout-btn span {
                display: none;
            }
            
            .logout-btn i {
                margin-right: 0;
            }
        }
        
        @media (max-width: 768px) {
            .welcome-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .last-login {
                text-align: left;
            }
            
            .stats-grid, .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .recent-table th:nth-child(2), 
            .recent-table td:nth-child(2),
            .recent-table th:nth-child(4), 
            .recent-table td:nth-child(4) {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div class="admin-logo">
            <i class="fas fa-store"></i>
            <span>E-Commerce Admin</span>
        </div>
        
        <div class="nav-items">
            <a href="dashboard.php" class="nav-item active">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="view_products.php" class="nav-item">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
            <a href="manage_categories.php" class="nav-item">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
            <a href="view_customers.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Customers</span>
            </a>
        </div>
        
        <div class="logout-wrap">
            <a href="admin_logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
    
    <!-- Main Content Area -->
    <main class="admin-main">
        <!-- Welcome Header -->
        <div class="welcome-header">
            <div class="welcome-text">
                <h1>Welcome, Admin!</h1>
                <p>Here's what's happening with your store today.</p>
            </div>
            <div class="last-login">
                <p>Last login: <?= date("M d, Y H:i") ?></p>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-products">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="stat-value"><?= $product_count ?></div>
                <div class="stat-label">Total Products</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-categories">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-value"><?= $categories_count ?></div>
                <div class="stat-label">Categories</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-customers">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?= $customer_count ?></div>
                <div class="stat-label">Customers</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-stock">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="stat-value"><?= $total_stock ?></div>
                <div class="stat-label">Total Stock</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-inventory-revenue">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">₹<?= number_format($total_inventory_revenue, 0) ?></div>
                <div class="stat-label">Total Inventory Revenue</div>
            </div>
        </div>
        
        <!-- Dashboard Panels -->
        <div class="dashboard-panels">
            <!-- Recent Products Panel -->
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Recent Products</h2>
                    <a href="view_products.php" class="panel-action">
                        View All <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <table class="recent-table">
                    <thead>
                        <tr >
                            <th style="text-align: center;">Product</th>
                            <th style="text-align: center;">Category</th>
                            <th style="text-align: center;">Price</th>
                            <th style="text-align: center;">Stock</th>
                            <th style="text-align: center;">Added</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_products->num_rows > 0): ?>
                            <?php while ($product = $recent_products->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="product-mini">
                                            <?php if ($product['image']): ?>
                                                <img src="../images/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                            <?php else: ?>
                                                <div class="no-mini-image">No Image</div>
                                            <?php endif; ?>
                                            <p class="product-mini-name"><?= htmlspecialchars($product['name']) ?></p>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($product['category_name'] ?: 'Uncategorized') ?></td>
                                    <td>₹<?= number_format($product['price'], 0) ?></td>
                                    <td>
                                        <?php if ($product['quantity'] > 0): ?>
                                            <?= $product['quantity'] ?>
                                        <?php else: ?>
                                            <span style="color: var(--danger); font-weight: bold;">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date("M d", strtotime($product['created_at'])) ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?= $product['id'] ?>" style="color: var(--accent); margin-right: 10px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_product.php?id=<?= $product['id'] ?>" style="color: var(--danger);" 
                                           onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px;">
                                    No products found. <a href="add_product.php" style="color: var(--primary);">Add your first product</a>.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Recent Customers Panel -->
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Recent Customers</h2>
                    <a href="view_customers.php" class="panel-action">
                        View All <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <div class="customer-list">
                    <?php if ($recent_customers->num_rows > 0): ?>
                        <?php while ($customer = $recent_customers->fetch_assoc()): ?>
                            <div class="customer-item">
                                <div class="customer-avatar">
                                    <?= strtoupper(substr($customer['username'], 0, 1)) ?>
                                </div>
                                <div class="customer-info">
                                    <h3 class="customer-name"><?= htmlspecialchars($customer['username']) ?></h3>
                                    <p class="customer-email"><?= htmlspecialchars($customer['email']) ?></p>
                                </div>
                                <div class="customer-status <?= $customer['is_blocked'] ? 'status-blocked' : 'status-active' ?>">
                                    <?= $customer['is_blocked'] ? 'Blocked' : 'Active' ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 20px; color: var(--text-light);">
                            No customers found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Action Cards -->
        <div class="quick-actions">
            <a href="add_product.php" class="action-card">
                <div class="action-icon action-add">
                    <i class="fas fa-plus"></i>
                </div>
                <h3 class="action-title">Add New Product</h3>
                <p class="action-desc">Create a new product in your store</p>
            </a>
            
            <a href="view_products.php" class="action-card">
                <div class="action-icon action-view">
                    <i class="fas fa-box"></i>
                </div>
                <h3 class="action-title">Manage Products</h3>
                <p class="action-desc">Edit, update or delete products</p>
            </a>
            
            <a href="manage_categories.php" class="action-card">
                <div class="action-icon action-categories">
                    <i class="fas fa-tags"></i>
                </div>
                <h3 class="action-title">Manage Categories</h3>
                <p class="action-desc">Create and organize categories</p>
            </a>
            
            <a href="view_customers.php" class="action-card">
                <div class="action-icon action-customers">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="action-title">Manage Customers</h3>
                <p class="action-desc">View and manage customer accounts</p>
            </a>
        </div>
    </main>
</body>
</html>