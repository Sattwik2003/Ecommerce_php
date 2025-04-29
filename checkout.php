<?php
session_start();
require('fpdf/fpdf.php'); // Include FPDF library

$conn = new mysqli("localhost", "root", "", "ecommerce");

// Fetch customer details
$customer_id = $conn->query("SELECT id FROM customers WHERE username='{$_SESSION['user']}'")->fetch_assoc()['id'];
$customer = $conn->query("SELECT * FROM customers WHERE id=$customer_id")->fetch_assoc();

// Fetch cart items
$cart = $conn->query("SELECT c.*, p.id AS product_id, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.customer_id=$customer_id");

$total = 0;
$items = [];
while ($item = $cart->fetch_assoc()) {
    $item_total = $item['price'] * $item['quantity'];
    $total += $item_total;
    $items[] = [
        'product_id' => $item['product_id'],
        'name' => $item['name'],
        'price' => $item['price'],
        'quantity' => $item['quantity'],
        'total' => $item_total
    ];
}

// Function to generate the PDF receipt
function generatePDFReceipt($customer, $items, $total) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Store details
    $pdf->Cell(0, 10, 'Store Name: My E-Commerce Store', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Store Address: 123 Main Street, City, Country', 0, 1, 'C');
    $pdf->Ln(10);

    // Customer details
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Customer Details', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Name: ' . $customer['username'], 0, 1);
    $pdf->Cell(0, 10, 'Email: ' . $customer['email'], 0, 1);
    $pdf->Ln(10);

    // Product details
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Product Details', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(30, 10, 'Product ID', 1);
    $pdf->Cell(70, 10, 'Product Name', 1);
    $pdf->Cell(30, 10, 'Quantity', 1);
    $pdf->Cell(30, 10, 'Price', 1);
    $pdf->Cell(30, 10, 'Total', 1);
    $pdf->Ln();

    foreach ($items as $item) {
        $pdf->Cell(30, 10, $item['product_id'], 1);
        $pdf->Cell(70, 10, $item['name'], 1);
        $pdf->Cell(30, 10, $item['quantity'], 1);
        $pdf->Cell(30, 10, 'Rs.' . number_format($item['price'], 2), 1);
        $pdf->Cell(30, 10, 'Rs.' . number_format($item['total'], 2), 1);
        $pdf->Ln();
    }

    // Total price
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Grand Total: Rs.' . number_format($total, 2), 0, 1, 'R');

    // Output the PDF
    $pdf->Output('D', 'Bill_Receipt.pdf'); // 'D' forces download
    exit();
}

// Generate the PDF receipt and clear the cart
if (isset($_GET['checkout'])) {
    generatePDFReceipt($customer, $items, $total);

    // Clear the cart after generating the receipt
    $conn->query("DELETE FROM cart WHERE customer_id=$customer_id");
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Complete</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f5f7;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .checkout-container {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .checkout-container a, .checkout-container button {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background: #f07cab;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center; /* Center the text inside the buttons */
        }
        .checkout-container a:hover, .checkout-container button:hover {
            background: #e26a98;
        }
    </style>
    <script>
        function clearCart() {
            // Send an AJAX request to clear the cart
            fetch('clear_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ customer_id: <?= $customer_id ?> })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cart cleared successfully!');
                    location.reload(); // Reload the page to reflect the cleared cart
                } else {
                    alert('Failed to clear cart: ' + data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</head>
<body>
    <div class="checkout-container">
        <h2>Checkout Complete!</h2>
        <p>Total Paid: ₹<?= number_format($total, 2) ?></p>
        <a href="?checkout=1">Download Receipt</a>
        <button onclick="clearCart()">Clear Cart</button>
        <a href="index.php">Continue Shopping</a>
    </div>
</body>
</html>
