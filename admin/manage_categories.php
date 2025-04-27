<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ecommerce");

// Add category
if (isset($_POST['add'])) {
    $name = $conn->real_escape_string($_POST['name']);
    if (!empty($name)) {
        $conn->query("INSERT INTO categories (name) VALUES ('$name')");
        header("Location: manage_categories.php");
        exit();
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id=$id");
    header("Location: manage_categories.php");
    exit();
}

// Fetch categories
$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>Manage Categories</header>

<div class="form-container">
    <h2>Add New Category</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Category Name" required>
        <input type="submit" name="add" value="Add Category" class="btn">
    </form>
</div>

<div style="margin-top: 40px; text-align: center;">
    <h2>Existing Categories</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $categories->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td>
                    <a class="button-link" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this category?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<a href="dashboard.php" class="button-link">⬅️ Back to Dashboard</a>

</body>
</html>
