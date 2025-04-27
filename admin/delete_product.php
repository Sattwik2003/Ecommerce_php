<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ecommerce");

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Optionally delete image file too (not shown here)

    $conn->query("DELETE FROM products WHERE id=$id");
}

header("Location: view_products.php");
exit();
?>
