<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

$customer_id = $conn->query("SELECT id FROM customers WHERE username='{$_SESSION['user']}'")->fetch_assoc()['id'];
$cart = $conn->query("SELECT c.*, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.customer_id=$customer_id");

$total = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <script src="js/cart.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f5f7;
            background-image: linear-gradient(135deg, #f9f5f7 0%, #fff6f9 100%);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: var(--secondary);
            text-align: center;
            margin: 20px 0;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
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
            font-size: 14px;
        }

        td {
            color: var(--text-dark);
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: #fafafa;
        }

        .actions a {
            color: var(--danger);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .actions a:hover {
            color: var(--danger-dark);
        }

        h3 {
            text-align: center;
            font-size: 24px;
            color: var(--primary);
            margin: 20px 0;
        }

        a.checkout-btn {
            display: inline-block;
            margin: 30px auto;
            text-align: center;
            padding: 15px 30px;
            background:rgb(240, 167, 255); /* Contrasting blue color */
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            border-radius: 8px;
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3); /* Adjusted shadow */
            position: relative;
            left: 50%;
            transform: translateX(-50%);
        }

        a.checkout-btn:hover {
            background:rgb(223, 62, 255); /* Darker shade of blue for hover effect */
            transform: translateX(-50%) scale(1.05);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.5); /* Adjusted hover shadow */
        }

        .empty-cart {
            text-align: center;
            font-size: 20px;
            color: var(--danger);
            margin: 30px 0;
        }
    </style>
</head>
<body>
<h2>Your Cart</h2>
<?php if ($cart->num_rows > 0): ?>
    <table>
        <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th>Actions</th></tr>
        <?php while ($item = $cart->fetch_assoc()): 
            $total += $item['price'] * $item['quantity'];
        ?>
        <tr>
            <td><?= $item['name'] ?></td>
            <td>Rs.<?= $item['price'] ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>Rs.<?= $item['price'] * $item['quantity'] ?></td>
            <td class="actions">
                <a href="remove_from_cart.php?id=<?= $item['id'] ?>">Remove</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Grand Total: Rs.<?= $total ?></h3>
    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
<?php else: ?>
<div class="empty-cart">
    <i class="fas fa-shopping-cart" style="font-size: 50px; color: var(--danger); margin-bottom: 10px;"></i>
    <p>Your Cart is Empty</p>
</div>
<?php endif; ?>
</body>
</html>
