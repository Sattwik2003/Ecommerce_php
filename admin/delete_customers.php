<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
@include 'conn.php'; // Include the database connection file

$id = $_GET['id'];
$conn->query("DELETE FROM customers WHERE id=$id");

header("Location: view_customers.php");
?>
