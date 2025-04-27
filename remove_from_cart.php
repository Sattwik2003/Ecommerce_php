<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

$item_id = $_GET['id'];
$conn->query("DELETE FROM cart WHERE id=$item_id");

header("Location: cart.php");
?>
