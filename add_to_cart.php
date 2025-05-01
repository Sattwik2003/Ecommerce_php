<?php
include 'check_customer_status.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: customer_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ecommerce");

$customer_id = $conn->query("SELECT id FROM customers WHERE username='{$_SESSION['user']}'")->fetch_assoc()['id'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];

$exists = $conn->query("SELECT * FROM cart WHERE customer_id=$customer_id AND product_id=$product_id");
if ($exists->num_rows > 0) {
    $conn->query("UPDATE cart SET quantity = quantity + $quantity WHERE customer_id=$customer_id AND product_id=$product_id");
} else {
    $conn->query("INSERT INTO cart (customer_id, product_id, quantity) VALUES ($customer_id, $product_id, $quantity)");
}

header("Location: cart.php");
?>