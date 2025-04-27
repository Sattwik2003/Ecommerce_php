<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

$customer_id = $conn->query("SELECT id FROM customers WHERE username='{$_SESSION['user']}'")->fetch_assoc()['id'];
$cart = $conn->query("SELECT c.*, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.customer_id=$customer_id");

$total = 0;
while ($item = $cart->fetch_assoc()) {
    $total += $item['price'] * $item['quantity'];
}

$conn->query("DELETE FROM cart WHERE customer_id=$customer_id");

echo "<h2>Checkout Complete!</h2>";
echo "<p>Total Paid: $$total</p>";
echo "<a href='index.php'>Continue Shopping</a>";
?>
