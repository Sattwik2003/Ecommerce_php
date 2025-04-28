<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

$username = $_POST['username'];
$password = $_POST['password'];

$result = $conn->query("SELECT * FROM customers WHERE username='$username' AND password='$password'");
if ($result->num_rows > 0) {
    $_SESSION['user'] = $username;

    // Check if there's a redirect URL stored in session
    if (isset($_SESSION['redirect_after_login'])) {
        $redirect_url = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']); // Clear the stored URL
        header("Location: $redirect_url");
        exit();
    } else {
        // Default redirect if no specific page was requested
        header("Location: index.php");
        exit();
    }
} else {
    // Save error message
    $_SESSION['login_error'] = "Invalid username or password";

    // Redirect back to login page
    header("Location: customer_login.php");
    exit();
}
