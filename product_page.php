<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

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

        .product-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 20px;
        }

        .product-image {
            flex: 1;
            max-width: 400px;
        }

        .product-image img {
            width: 100%;
            border-radius: 10px;
        }

        .product-details {
            flex: 2;
        }

        .product-title {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .product-category {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .product-price {
            font-size: 24px;
            font-weight: 700;
            color: #f07cab;
            margin-bottom: 20px;
        }

        .product-description {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .add-to-cart {
            display: inline-block;
            padding: 10px 20px;
            background: #f07cab;
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .add-to-cart:hover {
            background: #e26a98;
        }
    </style>
</head>
<body>
    <div class="product-container">
        <div class="product-image">
            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="product-details">
            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="product-category">Category: <?= htmlspecialchars($product['category_name']) ?></p>
            <p class="product-price">₹<?= number_format($product['price'], 2) ?></p>
            <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="number" name="quantity" value="1" min="1">
                    <button type="submit">Add to Cart</button>
                </form>
        </div>
    </div>
</body>
</html>