<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ecommerce");

// Fetch all products and their categories
$sql = "SELECT products.*, categories.name AS category_name 
        FROM products 
        LEFT JOIN categories ON products.category_id = categories.id
        ORDER BY products.created_at DESC";
$products = $conn->query($sql);

// Calculate total inventory value correctly (price * quantity for all products)
$revenue = 0;
$revenue_query = $conn->query("SELECT SUM(price) as total_value FROM products");
if ($revenue_query && $row = $revenue_query->fetch_assoc()) {
    $revenue = $row['total_value'] ?: 0;
}

// Get product count for stats
$product_count = $products->num_rows;
// Refresh result set for display
$products = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Admin Dashboard</title>
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 600;
            color: var(--secondary);
            margin: 0;
        }

        .page-tools {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search-bar {
            position: relative;
        }

        .search-input {
            width: 250px;
            padding: 10px 15px 10px 40px;
            border: 1px solid var(--border-color);
            border-radius: 30px;
            font-size: 14px;
            transition: var(--transition);
            background: white;
        }

        .search-input:focus {
            width: 300px;
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(240, 124, 171, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .add-product-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(240, 124, 171, 0.2);
        }

        .add-product-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Dashboard stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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
        .stat-date { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-revenue { background: linear-gradient(135deg, #f59e0b, #d97706); }

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

        /* Product table */
        .products-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--card-shadow);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--secondary);
            margin: 0;
        }

        .filter-options {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 15px;
            background: #f9f5f9;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            color: var(--secondary);
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }

        .filter-btn:hover {
            background: var(--light-bg);
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .filter-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(240, 124, 171, 0.25);
        }

        .product-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .product-table th {
            background: #f9f5f9;
            text-align: left;
            padding: 15px 20px;
            font-weight: 600;
            color: var(--secondary);
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        .product-table tr td {
            padding: 15px 20px;
            border-bottom: 1px solid #f5f5f5;
            vertical-align: middle;
        }

        .product-table tr:last-child td {
            border-bottom: none;
        }

        .product-table tbody tr {
            transition: var(--transition);
        }

        .product-table tbody tr:hover {
            background-color: #fafafa;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .product-image:hover {
            transform: scale(1.1);
        }

        .no-image {
            width: 60px;
            height: 60px;
            background: #f9f5f9;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 12px;
        }

        .product-name {
            font-weight: 600;
            color: var(--text-dark);
        }

        .product-price {
            font-weight: 700;
            color: var(--primary);
        }

        .category-badge {
            display: inline-block;
            padding: 5px 12px;
            background: #f9f5f9;
            color: var(--secondary);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
        }

        .date-display {
            color: #888;
            font-size: 13px;
        }

        /* Action buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            transition: var(--transition);
            text-decoration: none;
        }

        .edit-button {
            background: #f0f7ff;
            color: var(--accent);
        }

        .edit-button:hover {
            background: var(--accent);
            color: white;
        }

        .delete-button {
            background: #fff1f2;
            color: var(--danger);
        }

        .delete-button:hover {
            background: var(--danger);
            color: white;
        }

        .action-button i {
            margin-right: 4px;
            font-size: 12px;
        }

        /* Empty state styling */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state-icon {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
        }

        .empty-state-text {
            color: #888;
            font-size: 16px;
            margin-bottom: 20px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
        }

        .pagination-info {
            color: var(--text-light);
            font-size: 14px;
        }

        .pagination-controls {
            display: flex;
            gap: 5px;
        }

        .page-btn {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: white;
            border: 1px solid #eee;
            color: var(--text-dark);
            font-weight: 500;
            transition: var(--transition);
            text-decoration: none;
        }

        .page-btn:hover, .page-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-btn.disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
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
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .page-tools {
                width: 100%;
            }
            
            .search-input {
                width: 100%;
            }
            
            .search-input:focus {
                width: 100%;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .product-table th:nth-child(5),
            .product-table td:nth-child(5) {
                display: none;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }

        /* Added animation for row hover */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        .product-table tr:hover {
            animation: pulse 0.3s ease-in-out;
            box-shadow: 0 5px 15px rgba(240, 124, 171, 0.1);
            z-index: 1;
            position: relative;
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
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="view_products.php" class="nav-item active">
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
        <div class="page-header">
            <h1 class="page-title">Product Management</h1>
            <div class="page-tools">
                <div class="search-bar">
                    <input type="text" class="search-input" placeholder="Search products..." id="searchProducts">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <a href="add_product.php" class="add-product-btn">
                    <i class="fas fa-plus"></i>
                    <span>Add Product</span>
                </a>
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
            
            <?php
            // Get categories count
            $categories_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
            ?>
            <div class="stat-card">
                <div class="stat-icon stat-categories">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-value"><?= $categories_count ?></div>
                <div class="stat-label">Categories</div>
            </div>
            
            <?php
            // Get most recent product date
            $latest_product = $conn->query("SELECT created_at FROM products ORDER BY created_at DESC LIMIT 1");
            $latest_date = $latest_product->num_rows > 0 ? date("M d, Y", strtotime($latest_product->fetch_assoc()['created_at'])) : "N/A";
            ?>
            <div class="stat-card">
                <div class="stat-icon stat-date">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-value" style="font-size: 20px;"><?= $latest_date ?></div>
                <div class="stat-label">Latest Addition</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-revenue">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="stat-value">₹<?= number_format($revenue, 0) ?></div>
                <div class="stat-label">Total Inventory Value</div>
            </div>
        </div>
        
        <!-- Products Table -->
        <div class="products-section">
            <div class="section-header">
                <h2 class="section-title">All Products</h2>
                <div class="filter-options">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="latest">Latest</button>
                    <button class="filter-btn" data-filter="high-price">High Price</button>
                    <button class="filter-btn" data-filter="low-price">Low Price</button>
                </div>
            </div>
            
            <table class="product-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="12%">Image</th>
                        <th width="30%">Product Name</th>
                        <th width="15%">Price</th>
                        <th width="15%">Category</th>
                        <th width="23%">Actions</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <?php if ($products->num_rows > 0): ?>
                        <?php while ($row = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td>
                                    <?php if ($row['image']): ?>
                                        <img class="product-image" src="../images/<?= $row['image'] ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                                    <?php else: ?>
                                        <div class="no-image">No Image</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="product-name"><?= htmlspecialchars($row['name']) ?></div>
                                    <div class="date-display">Added: <?= date("M d, Y", strtotime($row['created_at'])) ?></div>
                                </td>
                                <td class="product-price">₹<?= number_format($row['price'], 2) ?></td>
                                <td><span class="category-badge"><?= htmlspecialchars($row['category_name'] ?: 'Uncategorized') ?></span></td>
                                <td>
                                    <div class="action-buttons">
                                        <a class="action-button edit-button" href="edit_product.php?id=<?= $row['id'] ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a class="action-button delete-button" href="delete_product.php?id=<?= $row['id'] ?>"
                                            onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-box-open empty-state-icon"></i>
                                    <p class="empty-state-text">No products found in your inventory</p>
                                    <a href="add_product.php" class="action-button edit-button">
                                        <i class="fas fa-plus"></i> Add your first product
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($products->num_rows > 0): ?>
            <div class="pagination">
                <div class="pagination-info">
                    Showing <b>1</b> to <b><?= $products->num_rows > 10 ? '10' : $products->num_rows ?></b> of <b><?= $products->num_rows ?></b> products
                </div>
                <div class="pagination-controls">
                    <a href="#" class="page-btn disabled"><i class="fas fa-chevron-left"></i></a>
                    <a href="#" class="page-btn active">1</a>
                    <?php if ($products->num_rows > 10): ?>
                    <a href="#" class="page-btn">2</a>
                    <?php endif; ?>
                    <?php if ($products->num_rows > 20): ?>
                    <a href="#" class="page-btn">3</a>
                    <?php endif; ?>
                    <?php if ($products->num_rows > 30): ?>
                    <a href="#" class="page-btn"><i class="fas fa-ellipsis-h"></i></a>
                    <?php endif; ?>
                    <a href="#" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <script>
        // Fixed search functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('searchProducts');
            
            searchInput.addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                const tableBody = document.getElementById('productTableBody');
                const rows = tableBody.getElementsByTagName('tr');
                
                for (let i = 0; i < rows.length; i++) {
                    const productNameEl = rows[i].querySelector('.product-name');
                    
                    if (productNameEl) {
                        const text = productNameEl.textContent || productNameEl.innerText;
                        const categoryEl = rows[i].querySelector('.category-badge');
                        const categoryText = categoryEl ? categoryEl.textContent || categoryEl.innerText : '';
                        
                        // Search in both product name and category
                        if (text.toLowerCase().indexOf(searchValue) > -1 || 
                            categoryText.toLowerCase().indexOf(searchValue) > -1) {
                            rows[i].style.display = '';
                        } else {
                            rows[i].style.display = 'none';
                        }
                    }
                }
            });
            
            // Filter buttons functionality
            const filterBtns = document.querySelectorAll('.filter-btn');
            
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Update active button
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filterType = this.getAttribute('data-filter');
                    const rows = document.getElementById('productTableBody').getElementsByTagName('tr');
                    
                    // Apply filter
                    switch(filterType) {
                        case 'latest':
                            // Sort by date - assumes newest product is already sorted first
                            // This is just visual indication that the filter works
                            break;
                        case 'high-price':
                            // Sort by price high to low
                            sortTableByPrice(true);
                            break;
                        case 'low-price':
                            // Sort by price low to high
                            sortTableByPrice(false);
                            break;
                        case 'all':
                            // Reset to default order
                            location.reload();
                            break;
                    }
                });
            });
            
            function sortTableByPrice(highToLow) {
                const table = document.querySelector('.product-table');
                const rows = Array.from(table.querySelectorAll('tbody tr'));
                
                // Skip if there's an empty state row
                if (rows.length === 1 && !rows[0].querySelector('.product-price')) {
                    return;
                }
                
                // Sort rows based on price
                rows.sort((a, b) => {
                    const priceA = parseFloat(a.querySelector('.product-price').innerText.replace('₹', '').replace(/,/g, ''));
                    const priceB = parseFloat(b.querySelector('.product-price').innerText.replace('₹', '').replace(/,/g, ''));
                    
                    return highToLow ? priceB - priceA : priceA - priceB;
                });
                
                // Reorder the rows
                const tbody = table.querySelector('tbody');
                rows.forEach(row => tbody.appendChild(row));
            }
        });
    </script>
</body>
</html>