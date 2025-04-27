<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<h2>Admin Login</h2>
<form method="POST" action="dashboard.php">
    <input type="text" name="admin_username" placeholder="Admin Username" required><br>
    <input type="password" name="admin_password" placeholder="Password" required><br>
    <input type="submit" value="Login">
</form>
</body>
</html>
