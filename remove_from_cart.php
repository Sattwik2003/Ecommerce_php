<?php
session_start();
@include 'conn.php'; // Include the database connection file

$item_id = $_GET['id'];
$conn->query("DELETE FROM cart WHERE id=$item_id");

header("Location: cart.php");
?>
