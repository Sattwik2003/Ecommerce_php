<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

// Check if the customer is logged in
if (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];

    // Fetch customer details
    $customer = $conn->query("SELECT is_blocked FROM customers WHERE username='$username'")->fetch_assoc();

    // If the customer is blocked, log them out and show a message
    if ($customer && $customer['is_blocked']) {
        // Destroy the session
        session_destroy();

        // Redirect to the login page with an error message
        $_SESSION['login_error'] = "Your account has been blocked. Please contact support for assistance.";
        header("Location: customer_login.php");
        exit();
    }
}
?>