<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Customer Login</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .error-message {
            background-color: #fee2e7;
            border: 1px solid #f43f5e;
            color: #e11d48;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .form-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #5a2d82;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .form-footer a {
            color: #f07cab;
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Login to Your Account</h2>

        <?php
        // Display error message if it exists
        if (isset($_SESSION['login_error'])) {
            echo '<div class="error-message">' . $_SESSION['login_error'] . '</div>';
            unset($_SESSION['login_error']); // Clear the message after showing it
        }
        ?>

        <form method="POST" action="authenticate_customer.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>

        <div class="form-footer">
            Don't have an account? <a href="customer_register.php">Register Here</a>
        </div>
    </div>
</body>

</html>