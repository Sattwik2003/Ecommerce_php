<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ecommerce");

// Fetch all products and their categories
$sql = "SELECT products.*, categories.name AS category_name 
        FROM products 
        LEFT JOIN categories ON products.category_id = categories.id
        ORDER BY products.created_at DESC";
$products = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>Manage Products</header>

<div style="overflow-x:auto;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price (₹)</th>
                <th>Category</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $products->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td>
                    <?php if ($row['image']): ?>
                        <img src="../images/<?= $row['image'] ?>" width="50">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['price'] ?></td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a class="button-link" href="edit_product.php?id=<?= $row['id'] ?>">Edit</a>
                    <a class="button-link" href="delete_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure to delete?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<a href="dashboard.php" class="button-link">Back to Dashboard</a>

</body>
</html>
