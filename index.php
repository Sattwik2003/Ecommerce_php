<?php

session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

// Get all products with category information
$products = $conn->query("SELECT p.*, c.name as category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         ORDER BY p.created_at DESC");

// Get all categories for filter menu
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store | Premium Tech Products</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
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
            --light-bg: #fff6f9;
            --border-color: #f8d7e5;
            --text-dark: #333;
            --text-light: #666;
            --card-shadow: 0 10px 25px rgba(240, 124, 171, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f5f7;
            background-image: linear-gradient(135deg, #f9f5f7 0%, #fff6f9 100%);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Header styling */
        .main-header {
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .logo i {
            font-size: 28px;
            color: var(--primary);
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--secondary);
            transition: var(--transition);
        }

        .logo:hover .logo-text {
            color: var(--primary);
        }

        .main-nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-link {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 30px;
            transition: var(--transition);
        }

        .nav-link:hover {
            background-color: var(--light-bg);
            color: var(--primary);
        }

        .nav-link.active {
            background-color: var(--light-bg);
            color: var(--primary);
        }

        .nav-link i {
            margin-right: 6px;
        }

        .cart-link {
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--primary);
            color: white;
            font-size: 11px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 30px;
            background-color: var(--light-bg);
            color: var(--primary);
            text-decoration: none;
            cursor: pointer;
            position: relative;
            transition: var(--transition);
        }

        .user-menu:hover {
            background-color: var(--primary);
            color: white;
        }

        /* Hero section */
        .hero-section {
            position: relative;
            color: white;
            padding: 60px 0;
            text-align: center;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            clip-path: ellipse(80% 60% at 50% 40%);
            z-index: -1;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 18px;
            margin-bottom: 30px;
            font-weight: 300;
            line-height: 1.6;
        }

        .search-bar {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
            background-color: white;
            border-radius: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .search-input {
            flex: 1;
            border: none;
            padding: 16px 20px;
            font-size: 16px;
            outline: none;
            font-family: 'Poppins', sans-serif;
        }

        .search-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 16px 25px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }

        .search-btn:hover {
            background: var(--primary-dark);
        }

        /* Featured categories */
        .category-section {
            padding: 60px 0 30px 0;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            text-align: center;
            color: var(--secondary);
            margin-bottom: 15px;
        }

        .section-subtitle {
            text-align: center;
            color: var(--text-light);
            margin-bottom: 40px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .category-cards {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .category-card {
            width: 180px;
            height: 180px;
            background: white;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: var(--card-shadow);
            cursor: pointer;
            transition: var(--transition);
            overflow: hidden;
            position: relative;
            text-decoration: none;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            opacity: 0;
            transition: var(--transition);
            z-index: 1;
        }

        .category-card .icon {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 15px;
            transition: var(--transition);
            position: relative;
            z-index: 2;
        }

        .category-card .name {
            font-weight: 600;
            color: var(--text-dark);
            transition: var(--transition);
            position: relative;
            z-index: 2;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-card:hover::before {
            opacity: 0.9;
        }

        .category-card:hover .icon,
        .category-card:hover .name {
            color: white;
        }

        /* Product section */
        .products-section {
            padding: 30px 0 60px 0;
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 8px 16px;
            background-color: white;
            border: none;
            border-radius: 30px;
            color: var(--text-light);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .filter-tab:hover, .filter-tab.active {
            background-color: var(--primary);
            color: white;
        }

        .sort-dropdown {
            position: relative;
        }

        .sort-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background-color: white;
            border: none;
            border-radius: 30px;
            color: var(--text-light);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .product-card {
            background-color: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: var(--transition);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-10px);
        }

        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 5px 10px;
            background-color: var(--accent);
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 1;
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-info {
            padding: 20px;
        }

        .product-category {
            font-size: 13px;
            color: var(--text-light);
            margin-bottom: 8px;
            display: block;
        }

        .product-name {
            font-size: 18px;
            color: var(--text-dark);
            margin-bottom: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            display: block;
            line-height: 1.4;
        }

        .product-name:hover {
            color: var(--primary);
        }

        .product-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .product-price .original {
            text-decoration: line-through;
            font-size: 16px;
            color: var(--text-light);
            font-weight: 400;
        }

        .product-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 1px solid #eaeaea;
            border-radius: 6px;
            font-size: 14px;
            text-align: center;
        }

        .add-to-cart {
            flex: 1;
            padding: 10px;
            background-color: var(--primary);
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .add-to-cart:hover {
            background-color: var(--primary-dark);
        }

        .quick-view {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light-bg);
            border: none;
            border-radius: 6px;
            color: var(--primary);
            cursor: pointer;
            transition: var(--transition);
        }

        .quick-view:hover {
            background-color: var(--primary);
            color: white;
        }

        /* Featured products slider */
        .featured-section {
            padding: 60px 0;
            background-color: white;
            position: relative;
            overflow: hidden;
        }

        .featured-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 20%, var(--light-bg) 0%, transparent 20%),
                radial-gradient(circle at 80% 80%, var(--light-bg) 0%, transparent 20%);
            opacity: 0.7;
            z-index: 0;
        }

        .featured-content {
            position: relative;
            z-index: 1;
        }

        .swiper {
            width: 100%;
            padding: 20px 10px 40px 10px;
        }

        .swiper-button-next, 
        .swiper-button-prev {
            color: var(--primary);
        }

        .swiper-pagination-bullet-active {
            background-color: var(--primary);
        }

        /* Newsletter section */
        .newsletter-section {
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            padding: 80px 0;
            color: white;
            text-align: center;
        }

        .newsletter-title {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            margin-bottom: 15px;
        }

        .newsletter-subtitle {
            max-width: 600px;
            margin: 0 auto 30px auto;
            font-size: 16px;
            font-weight: 300;
        }

        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .newsletter-input {
            flex: 1;
            border: none;
            padding: 16px 20px;
            font-size: 16px;
            outline: none;
            font-family: 'Poppins', sans-serif;
        }

        .newsletter-btn {
            background-color: white;
            border: none;
            padding: 16px 25px;
            color: var(--primary);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
        }

        .newsletter-btn:hover {
            background-color: var(--light-bg);
        }

        /* Footer */
        .footer {
            background-color: white;
            padding: 60px 0 30px 0;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .footer-logo i {
            font-size: 28px;
            color: var(--primary);
        }

        .footer-logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--secondary);
        }

        .footer-description {
            color: var(--text-light);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .social-links {
            display: flex;
            gap: 10px;
        }

        .social-link {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light-bg);
            color: var(--primary);
            border-radius: 50%;
            transition: var(--transition);
        }

        .social-link:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        .footer-col-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-link {
            margin-bottom: 12px;
        }

        .footer-link a {
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-link a:hover {
            color: var(--primary);
            transform: translateX(5px);
        }

        .footer-bottom {
            border-top: 1px solid var(--border-color);
            margin-top: 40px;
            padding-top: 20px;
            text-align: center;
            color: var(--text-light);
        }

        /* Floating buttons */
        .float-btns {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 50;
        }

        .float-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: var(--transition);
            color: var(--primary);
        }

        .float-btn:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-5px);
        }

        /* Login button */
        .login-btn {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .login-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Responsive styles */
        @media (max-width: 991px) {
            .hero-title {
                font-size:.35px;
            }
            
            .hero-subtitle {
                font-size: 16px;
            }
            
            .products-header {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .hero-section {
                padding: 40px 0;
            }
            
            .hero-title {
                font-size: 28px;
            }
            
            .hero-subtitle {
                font-size: 15px;
                margin-bottom: 20px;
            }
            
            .search-input,
            .search-btn {
                padding: 12px;
                font-size: 14px;
            }
            
            .section-title {
                font-size: 26px;
            }
            
            .newsletter-title {
                font-size: 26px;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
            
            .newsletter-input,
            .newsletter-btn {
                width: 100%;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        /* Animation keyframes */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <!-- Floating Action Buttons -->
    <div class="float-btns">
        <a href="#top" class="float-btn" title="Go to top">
            <i class="fas fa-arrow-up"></i>
        </a>
        <?php if (isset($_SESSION['user'])): ?>
        <a href="cart.php" class="float-btn" title="View cart">
            <i class="fas fa-shopping-cart"></i>
        </a>
        <?php endif; ?>
    </div>

    <!-- Header -->
    <header class="main-header" id="top">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <i class="fas fa-store"></i>
                    <span class="logo-text">E-Commerce Store</span>
                </a>
                
                <nav class="main-nav">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a href="#products" class="nav-link">
                        <i class="fas fa-box"></i> Products
                    </a>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="cart.php" class="nav-link cart-link">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <span class="cart-count">
                                <?php 
                                    // Get cart count
                                    $customer_id = $conn->query("SELECT id FROM customers WHERE username='{$_SESSION['user']}'")->fetch_assoc()['id'];
                                    $cart_count = $conn->query("SELECT COUNT(*) as count FROM cart WHERE customer_id=$customer_id")->fetch_assoc()['count'];
                                    echo $cart_count;
                                ?>
                            </span>
                        </a>
                        <div class="user-menu">
                            <i class="fas fa-user"></i>
                            <span><?= htmlspecialchars($_SESSION['user']) ?></span>
                        </div>
                        <a href="logout.php" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="customer_login.php" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="customer_register.php" class="nav-link">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Discover Premium Tech Products</h1>
                <p class="hero-subtitle">Explore our collection of high-quality electronics and cutting-edge technology. Get the best deals on the latest products!</p>
                <!-- <form class="search-bar" action="index.php" method="GET">
                    <input type="text" name="search" placeholder="Search for products..." class="search-input">
                    <button type="submit" class="search-btn">Search</button>
                </form> -->
            </div>
        </div>
    </section>

    <!-- Category Section -->
    <section class="category-section">
        <div class="container">
            <h2 class="section-title">Browse Categories</h2>
            <p class="section-subtitle">Explore our wide range of product categories to find exactly what you're looking for.</p>
            
            <div class="category-cards">
                <?php
                // Define category icons (can be expanded)
                $category_icons = [
                    'Graphics Card' => 'fa-microchip',
                    'Computer' => 'fa-desktop',
                    'Laptop' => 'fa-laptop',
                    'Monitor' => 'fa-tv',
                    'Accessories' => 'fa-keyboard',
                    'Other' => 'fa-box'
                ];
                
                // Generate category cards
                if ($categories->num_rows > 0) {
                    while ($category = $categories->fetch_assoc()) {
                        $icon = isset($category_icons[$category['name']]) ? $category_icons[$category['name']] : 'fa-box';
                        echo '<a href="#" class="category-card" data-category="'.$category['id'].'">';
                        echo '<i class="fas '.$icon.' icon"></i>';
                        echo '<span class="name">'.htmlspecialchars($category['name']).'</span>';
                        echo '</a>';
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section" id="products">
        <div class="container">
            <h2 class="section-title">Our Products</h2>
            <p class="section-subtitle">Discover our collection of top-rated products, carefully selected for quality and performance.</p>
            
            <div class="products-header">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">All Products</button>
                    <button class="filter-tab" data-filter="new">New Arrivals</button>
                    <button class="filter-tab" data-filter="popular">Popular</button>
                </div>
                
                <div class="sort-dropdown">
                    <button class="sort-btn">
                        <i class="fas fa-sort"></i> Sort by: Latest
                    </button>
                </div>
            </div>
            
            <div class="products-grid">
                <?php if ($products->num_rows > 0): 
                    while ($product = $products->fetch_assoc()): ?>
                    <div class="product-card" data-category="<?= $product['category_id'] ?>">
                        <span class="product-badge">New</span>
                        <a href="product_page.php?id=<?= $product['id'] ?>">
                            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        </a>
                        <div class="product-info">
                            <span class="product-category"><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></span>
                            <a href="product_page.php?id=<?= $product['id'] ?>" class="product-name"><?= htmlspecialchars($product['name']) ?></a>
                            <div class="product-price">
                                <span>₹<?= number_format($product['price'], 2) ?></span>
                                <?php if (rand(0, 1)): // Random discount for demo ?>
                                    <span class="original">₹<?= number_format($product['price'] * 1.2, 2) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (isset($_SESSION['user'])): ?>
                                <form action="add_to_cart.php" method="POST" class="product-actions">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                                    <button type="submit" class="add-to-cart">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                    <a href="product_page.php?id=<?= $product['id'] ?>" class="quick-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </form>
                            <?php else: ?>
                                <div class="product-actions">
                                    <a href="customer_login.php" class="add-to-cart">
                                        <i class="fas fa-sign-in-alt"></i> Login to Buy
                                    </a>
                                    <a href="product_page.php?id=<?= $product['id'] ?>" class="quick-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else: ?>
                    <div style="text-align:center; grid-column: 1/-1; padding: 40px; color: var(--text-light);">
                        <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p>No products found! Check back later for new items.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <!-- <section class="newsletter-section">
        <div class="container">
            <h2 class="newsletter-title">Subscribe to Our Newsletter</h2>
            <p class="newsletter-subtitle">Get the latest updates, exclusive offers and special discounts delivered directly to your inbox.</p>
            
            <form class="newsletter-form" action="#" method="POST">
                <input type="email" placeholder="Enter your email address" class="newsletter-input" required>
                <button type="submit" class="newsletter-btn">Subscribe</button>
            </form>
        </div>
    </section> -->

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-store"></i>
                        <span class="footer-logo-text">E-Commerce Store</span>
                    </div>
                    <p class="footer-description">
                        Your one-stop destination for premium tech products and exceptional customer service.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-col-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li class="footer-link"><a href="#products"><i class="fas fa-chevron-right"></i> Products</a></li>
                        <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> About Us</a></li>
                        <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-col-title">Customer Area</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="customer_login.php"><i class="fas fa-chevron-right"></i> My Account</a></li>
                        <li class="footer-link"><a href="cart.php"><i class="fas fa-chevron-right"></i> My Cart</a></li>
                        <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Wishlist</a></li>
                        <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Track Order</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-col-title">Contact Us</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="#"><i class="fas fa-map-marker-alt"></i> 123 Tech Street, City, Country</a></li>
                        <li class="footer-link"><a href="#"><i class="fas fa-phone"></i> +1 234 567 8901</a></li>
                        <li class="footer-link"><a href="#"><i class="fas fa-envelope"></i> support@ecommerce.com</a></li>
                        <li class="footer-link"><a href="#"><i class="fas fa-clock"></i> Mon - Fri: 9:00AM - 6:00PM</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> E-Commerce Store. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Category filter
            const categoryCards = document.querySelectorAll('.category-card');
            const productCards = document.querySelectorAll('.product-card');
            
            categoryCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const categoryId = this.getAttribute('data-category');
                    
                    // Remove active class from all cards
                    categoryCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter products
                    productCards.forEach(product => {
                        if (categoryId === 'all' || product.getAttribute('data-category') === categoryId) {
                            product.style.display = '';
                        } else {
                            product.style.display = 'none';
                        }
                    });
                });
            });

            // Product filter tabs
            const filterTabs = document.querySelectorAll('.filter-tab');
            
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    
                    // Update active tab
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Here you would implement actual filtering logic
                    // For demo purposes, we just show all products
                    productCards.forEach(product => {
                        product.style.display = '';
                    });
                    
                    // Scroll to products section smoothly
                    document.getElementById('products').scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
            
            // Smooth scroll for navigation
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
            
            // Animate elements on scroll
            const animateElements = document.querySelectorAll('.section-title, .section-subtitle, .product-card, .category-card');
            
            function checkScroll() {
                animateElements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const elementVisible = 150;
                    
                    if (elementTop < window.innerHeight - elementVisible) {
                        element.style.opacity = '1';
                        element.style.animation = 'fadeIn 0.6s ease-out forwards';
                    }
                });
            }
            
            // Initial animation setup
            animateElements.forEach(element => {
                element.style.opacity = '0';
            });
            
            // Run on scroll
            window.addEventListener('scroll', checkScroll);
            
            // Run once on load
            checkScroll();
        });
    </script>
</body>
</html>