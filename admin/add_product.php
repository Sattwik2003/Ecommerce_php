<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

@include 'conn.php'; // Include the database connection file

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = $conn->real_escape_string($_POST['price']);
    $quantity = (int)$_POST['quantity'];
    $description = $conn->real_escape_string($_POST['description']);
    $category_id = (int)$_POST['category_id'];

    // Upload image
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    if (!empty($image_name)) {
        move_uploaded_file($image_tmp, "../images/" . $image_name);
    } else {
        $image_name = NULL;
    }

    // Insert into products table
    $sql = "INSERT INTO products (name, description, price, quantity, image, category_id) 
            VALUES ('$name', '$description', $price, $quantity , '$image_name', $category_id)";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Product added successfully!";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Product</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>Add New Product</header>

<div class="form-container">
    <?php if (isset($success_message)) echo "<p class='success'>$success_message</p>"; ?>
    <?php if (isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required><br>

        <textarea name="description" placeholder="Product Description" rows="5" required></textarea><br>

        <input type="number" step="0.01" name="price" placeholder="Product Price" required><br>

        <input type="number" step="0.01" name="quantity" placeholder="Quantity" required><br>

        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php while ($row = $categories->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <input type="file" name="image" accept="image/*"><br><br>

        <input type="submit" value="Add Product" class="btn">
    </form>
</div>

<a href="dashboard.php" class="button-link">Back to Dashboard</a>

</body>
</html>
