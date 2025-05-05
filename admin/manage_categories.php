<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

@include 'conn.php'; // Include the database connection file

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories | Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f07cab;
            --primary-dark: #e26a98;
            --secondary: #5a2d82;
            --secondary-dark: #4a2569;
            --danger: #f43f5e;
            --danger-dark: #e11d48;
            --light-bg: #fff6f9;
            --border-color: #f8d7e5;
            --text-dark: #333;
            --text-light: #666;
            --card-shadow: 0 10px 25px rgba(244, 114, 182, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: #f9f5f7;
            background-image: linear-gradient(135deg, #f9f5f7 0%, #fff6f9 100%);
            min-height: 100vh;
            color: var(--text-dark);
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }

        .admin-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            padding: 30px 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        .admin-logo {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--secondary);
            text-align: center;
            margin-bottom: 40px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-logo i {
            font-size: 24px;
            margin-right: 10px;
            color: var(--primary);
        }

        .nav-items {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: var(--transition);
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(240, 124, 171, 0.08);
            border-left-color: var(--primary);
            color: var(--primary);
        }

        .nav-item i {
            margin-right: 12px;
            font-size: 18px;
        }

        .logout-wrap {
            margin-top: auto;
            padding: 20px;
            border-top: 1px solid var(--border-color);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff1f5;
            color: var(--danger);
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background: var(--danger);
            color: white;
        }

        .logout-btn i {
            margin-right: 8px;
        }

        .admin-main {
            margin-left: 250px;
            padding: 30px;
        }

        .categories-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--card-shadow);
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background: var(--light-bg);
            color: var(--secondary);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
        }

        td {
            color: var(--text-dark);
        }

        .button-link {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .button-link:hover {
            background: var(--primary-dark);
        }
        .section-title, .nav-item, .logout-btn, .status-badge, .action-btn {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <div class="admin-logo">
            <i class="fas fa-store"></i>
            <span>E-Commerce Admin</span>
        </div>
        <div class="nav-items">
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="view_products.php" class="nav-item">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
            <a href="manage_categories.php" class="nav-item active">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
            <a href="view_customers.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Customers</span>
            </a>
        </div>
        <div class="logout-wrap">
            <a href="admin_logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>

    <main class="admin-main">
        <div class="categories-section">
            <h2 class="section-title">Manage Categories</h2>
            <div class="form-container">
                <h3>Add New Category</h3>
                <form method="POST">
                    <input type="text" name="name" placeholder="Category Name" required>
                    <input type="submit" name="add" value="Add Category" class="button-link">
                </form>
            </div>
            <h3>Existing Categories</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $categories->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                        <a class="button-link" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this category?')">
                <i class="fas fa-trash" style="margin-right: 5px;"></i> Delete
            </a>
                            
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
