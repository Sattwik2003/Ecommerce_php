<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ecommerce");

if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$id = (int)$_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $category_id = (int)$_POST['category_id'];

    // Handle image
    if (!empty($_FILES['image']['name'])) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($image_tmp, "../images/" . $image_name);
        $conn->query("UPDATE products SET image='$image_name' WHERE id=$id");
    }

    $conn->query("UPDATE products SET name='$name', price=$price, description='$description', category_id=$category_id WHERE id=$id");

    header("Location: view_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>Edit Product</header>

<div class="form-container">
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>

        <textarea name="description" rows="5" required><?= htmlspecialchars($product['description']) ?></textarea><br>

        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required><br>

        <select name="category_id" required>
            <?php while ($row = $categories->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= ($product['category_id'] == $row['id']) ? "selected" : "" ?>>
                    <?= htmlspecialchars($row['name']) ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        Current Image: 
        <?php if ($product['image']): ?>
            <img src="../images/<?= $product['image'] ?>" width="50"><br><br>
        <?php else: ?>
            No image<br><br>
        <?php endif; ?>

        <input type="file" name="image" accept="image/*"><br><br>

        <input type="submit" value="Update Product" class="btn">
    </form>
</div>

<a href="view_products.php" class="button-link">Back to Products</a>

</body>
</html>
