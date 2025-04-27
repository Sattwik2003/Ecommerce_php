<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

$username = $_POST['username'];
$password = $_POST['password'];

$result = $conn->query("SELECT * FROM customers WHERE username='$username' AND password='$password'");
if ($result->num_rows > 0) {
    $_SESSION['user'] = $username;
    header("Location: index.php");
} else {
    echo "Invalid credentials. <a href='customer_login.php'>Try again</a>";
}
?>
