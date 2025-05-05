<?php
include 'check_customer_status.php';
session_start();
@include 'conn.php'; // Include the database connection file

// Fetch product details
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $conn->query("SELECT products.*, categories.name AS category_name 
                         FROM products 
                         JOIN categories ON products.category_id = categories.id 
                         WHERE products.id = $product_id")->fetch_assoc();

if (!$product) {
    echo "Product not found!";
    exit();
}

// Store the current product page URL in session for redirect after login
$_SESSION['redirect_after_login'] = "product_page.php?id=" . $product_id;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> | Product Page</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f5f7;
        }

        /* Header/Navigation */
        .site-header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .site-title {
            font-family: 'Playfair Display', serif;
            color: #5a2d82;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #f07cab;
        }

        .product-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 30px;
        }

        .product-image {
            flex: 1;
            max-width: 500px;
        }

        .product-image img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .product-details {
            flex: 1;
            padding: 20px;
        }

        .product-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            font-family: 'Playfair Display', serif;
        }

        .product-category {
            display: inline-block;
            font-size: 14px;
            color: #f07cab;
            background: #fff6f9;
            padding: 5px 15px;
            border-radius: 20px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .product-price {
            font-size: 28px;
            font-weight: 700;
            color: #f07cab;
            margin-bottom: 20px;
            border-bottom: 1px solid #f1d1e0;
            padding-bottom: 20px;
        }

        .product-description {
            font-size: 16px;
            color: #555;
            line-height: 1.8;
            margin-bottom: 30px;
            text-align: justify;
        }

        /* Form styling */
        .add-to-cart-form {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .add-to-cart-form input[type="number"] {
            width: 80px;
            padding: 12px 15px;
            border: 1px solid #e5c1cf;
            border-radius: 8px;
            font-size: 16px;
            text-align: center;
        }

        .add-to-cart-form button {
            flex: 1;
            padding: 12px 25px;
            background: #f07cab;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            max-width: 250px;
        }

        .add-to-cart-form button:hover {
            background: #e26a98;
        }

        .cart-icon {
            font-size: 18px;
        }

        /* Not logged in message */
        .login-message {
            background: #fff6f9;
            border: 1px solid #f1d1e0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 15px;
            color: #333;
        }

        .login-message a {
            color: #f07cab;
            font-weight: 600;
            text-decoration: none;
        }

        .login-message a:hover {
            text-decoration: underline;
        }

        /* Back button */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #666;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: color 0.3s;
        }

        .back-button:hover {
            color: #f07cab;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .product-container {
                flex-direction: column;
            }

            .product-image {
                max-width: 100%;
            }

            .product-details {
                padding: 10px 0;
            }
        }
    </style>
</head>

<body>
    <!-- Header Navigation -->
    <header class="site-header">
        <a href="index.php" class="site-title">My E-Commerce Store</a>
        <div class="nav-links">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="customer_login.php">Login</a>
                <a href="customer_register.php">Register</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="product-container">
        <div class="product-image">
            <a href="index.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="product-details">
            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
            <span class="product-category"><?= htmlspecialchars($product['category_name']) ?></span>
            <p class="product-price">₹<?= number_format($product['price'], 2) ?></p>
            <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>

            <?php if (isset($_SESSION['user'])): ?>
                <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="number" name="quantity" value="1" min="1" max="10">
                    <button type="submit">
                        <i class="fas fa-shopping-cart cart-icon"></i> Add to Cart
                    </button>
                </form>
            <?php else: ?>
                <div class="login-message">
                    Please <a href="customer_login.php">login</a> to add this product to your cart.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>