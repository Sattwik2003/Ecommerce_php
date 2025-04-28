<!DOCTYPE html>
<html>
<head>
    <title>Customer Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<h2>Login</h2>
<form method="POST" action="authenticate_customer.php">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="submit" value="Login">
</form>
<p style="text-align: center;">Don't have an account? <a href="customer_register.php">Register Here</a></p>
</body>
</html>
