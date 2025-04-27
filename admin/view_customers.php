<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ecommerce");

// Toggle block/unblock action
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $customer = $conn->query("SELECT is_blocked FROM customers WHERE id=$id")->fetch_assoc();
    
    if ($customer) {
        $new_status = $customer['is_blocked'] ? 0 : 1;
        $conn->query("UPDATE customers SET is_blocked=$new_status WHERE id=$id");
    }
    header("Location: view_customers.php");
    exit();
}

// Fetch all customers
$customers = $conn->query("SELECT * FROM customers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Customers</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>Manage Customers</header>

<div style="margin-top: 30px; text-align: center;">
    <h2>Customer List</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while($row = $customers->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['is_blocked'] ? '<span style="color:red;">Blocked</span>' : '<span style="color:green;">Active</span>' ?></td>
            <td>
                <a class="button-link" href="?toggle=<?= $row['id'] ?>">
                    <?= $row['is_blocked'] ? "Unblock" : "Block" ?>
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a class="button-link" href="dashboard.php">⬅️ Back to Dashboard</a>
</div>

</body>
</html>
