<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

$products = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<header>
    <h1>My E-Commerce Store</h1>
    <?php if (isset($_SESSION['user'])): ?>
        <a href="cart.php">View Cart</a> |
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="customer_login.php">Login</a> |
        <a href="customer_register.php">Register</a>
    <?php endif; ?>
</header>

<div class="products">
<?php while($row = $products->fetch_assoc()): ?>
    <div class="product-card">
        <img src="images/<?php echo $row['image']; ?>" width="150px">
        <h3><?php echo $row['name']; ?></h3>
        <p>Rs.<?php echo $row['price']; ?></p>
        <?php if (isset($_SESSION['user'])): ?>
            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <input type="number" name="quantity" value="1" min="1">
                <button type="submit">Add to Cart</button>
            </form>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
</div>
</body>
</html>
