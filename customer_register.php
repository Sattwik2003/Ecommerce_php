<!DOCTYPE html>
<html>
<head>
    <title>Customer Registration</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<h2>Register</h2>
<form method="POST" action="register_customer.php">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="submit" value="Register">
</form>
<p>Already have an account? <a href="customer_login.php">Login Here</a></p>
</body>
</html>
