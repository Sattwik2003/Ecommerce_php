<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

$customer_id = $conn->query("SELECT id FROM customers WHERE username='{$_SESSION['user']}'")->fetch_assoc()['id'];
$cart = $conn->query("SELECT c.*, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.customer_id=$customer_id");

$total = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/cart.js"></script>
</head>
<body>
<h2>Your Cart</h2>
<table>
<tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th>Actions</th></tr>
<?php while ($item = $cart->fetch_assoc()): 
$total += $item['price'] * $item['quantity'];
?>
<tr>
    <td><?= $item['name'] ?></td>
    <td>Rs.<?= $item['price'] ?></td>
    <td><?= $item['quantity'] ?></td>
    <td>Rs.<?= $item['price'] * $item['quantity'] ?></td>
    <td>
        <a href="remove_from_cart.php?id=<?= $item['id'] ?>">Remove</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<h3>Grand Total: Rs.<?= $total ?></h3>
<a href="checkout.php">Proceed to Checkout</a>
</body>
</html>
