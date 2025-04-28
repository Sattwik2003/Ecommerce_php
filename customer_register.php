<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Customer Registration | E-Commerce Store</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f07cab;
            --primary-dark: #e26a98;
            --secondary: #5a2d82;
            --secondary-dark: #4a2569;
            --danger: #f43f5e;
            --danger-dark: #e11d48;
            --light-bg: #fff6f9;
            --border-color: #f8d7e5;
            --text-dark: #333;
            --text-light: #666;
            --card-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f5f7;
            background-image: linear-gradient(135deg, #f9f5f7 0%, #fff6f9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-message {
            background-color: #fee2e7;
            border: 1px solid #f43f5e;
            color: #e11d48;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-message i {
            color: #e11d48;
            font-size: 16px;
        }

        .form-container {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
        }

        .header-logo {
            text-align: center;
            margin-bottom: 30px;
            font-family: 'Playfair Display', serif;
            color: var(--secondary);
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .header-logo i {
            color: var(--primary);
            font-size: 24px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: var(--secondary);
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 15px;
        }

        .form-group .input-with-icon {
            position: relative;
        }

        .form-group .input-with-icon i {
            position: absolute;
            right: 10px;
            top: 45%;
            transform: translateY(-50%);
            color: #aaa;
            transition: var(--transition);
            pointer-events: none;
            /* Prevents the icon from blocking input interaction */
            z-index: 10;
            /* Ensure icon stays above the input */
            opacity: 0.7;
            font-size: 16px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            /* Left padding to push placeholder text after icon */
            border: 1px solid #eaeaea;
            border-radius: 10px;
            font-size: 15px;
            transition: var(--transition);
            background: #f5f8ff;
            /* Light blue background for better contrast */
            color: var(--text-dark);
            position: relative;
        }

        .form-group input::placeholder {
            color: #aaa;
            font-weight: 400;
            opacity: 0.7;
            /* Avoid text appearing behind the icon */
            margin-left: 5px;
            transition: var(--transition);
        }

        /* Push the placeholder text a bit to the right */
        .form-group input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px #f5f8ff inset;
            -webkit-text-fill-color: var(--text-dark);
        }

        .form-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(240, 124, 171, 0.1);
            outline: none;
            background-color: #fff;
        }

        .form-group input:focus+i {
            color: var(--primary);
            opacity: 1;
        }

        .submit-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 15px;
            box-shadow: 0 4px 10px rgba(240, 124, 171, 0.2);
            gap: 8px;
        }

        .submit-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .submit-btn i {
            font-size: 16px;
        }

        .form-footer {
            text-align: center;
            margin-top: 25px;
            color: var(--text-light);
            font-size: 15px;
            border-top: 1px solid #f5f5f5;
            padding-top: 25px;
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .form-footer a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .back-to-home {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            color: var(--text-light);
            text-decoration: none;
            font-size: 14px;
            transition: var(--transition);
        }

        .back-to-home:hover {
            color: var(--primary);
        }

        .back-to-home i {
            font-size: 12px;
        }

        @media (max-width: 500px) {
            .form-container {
                padding: 25px;
            }
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="header-logo">
            <i class="fas fa-store"></i>
            <span>E-Commerce Store</span>
        </div>

        <h2>Create Your Account</h2>

        <?php
        // Display error message if it exists
        if (isset($_SESSION['register_error'])) {
            echo '<div class="error-message"><i class="fas fa-exclamation-circle"></i>' . $_SESSION['register_error'] . '</div>';
            unset($_SESSION['register_error']); // Clear the message after showing it
        }
        ?>

        <form method="POST" action="register_customer.php">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-with-icon">
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    <i class="fas fa-user"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-with-icon">
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                    <i class="fas fa-envelope"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-with-icon">
                    <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="form-footer">
            Already have an account? <a href="customer_login.php">Login Here</a>
        </div>

        <a href="index.php" class="back-to-home">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>
</body>

</html>