<?php
@include 'conn.php'; // Include the database connection file

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

if (!empty($username) && !empty($password) && !empty($email)) {
    $conn->query("INSERT INTO customers (username, email, password) VALUES ('$username', '$email', '$password')");
    header("Location: customer_login.php");
} else {
    echo "Please fill all fields.";
}
?>
