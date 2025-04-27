<?php
session_start();

// Hardcoded admin credentials
$admin_user = "admin";
$admin_pass = "admin123";

// Admin login check
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['admin_username'] == $admin_user && $_POST['admin_password'] == $admin_pass) {
        $_SESSION['admin'] = $admin_user;
    } else {
        die("Wrong credentials! <a href='admin_login.php'>Try again</a>");
    }
}

// Session validation
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>Admin Dashboard</header>

<div style="text-align: center; margin-top: 30px;">
    <h2>Welcome, Admin!</h2>

    <div style="margin-top: 30px;">
        <a class="button-link" href="add_product.php">➕ Add New Product</a> |
        <a class="button-link" href="view_products.php">🛒 Manage Products</a> |
        <a class="button-link" href="view_customers.php">👤 Manage Customers</a> |
        <a class="button-link" href="manage_categories.php">📂 Manage Categories</a> |
        <a class="button-link" style="background-color: #f44336;" href="admin_logout.php">🚪 Logout</a>
    </div>
</div>

</body>
</html>
