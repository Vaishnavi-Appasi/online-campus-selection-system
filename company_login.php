<?php
session_start();
include "connection.php";

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    if (empty($email) || empty($password)) {
        $loginError = "Please fill in all fields.";
    } else {
        $email = mysqli_real_escape_string($con, $email);
        $password = mysqli_real_escape_string($con, $password);

        $query = "SELECT * FROM company WHERE email = '$email'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) === 1) {
            $company = mysqli_fetch_assoc($result);

            if (password_verify($password, $company['password'])) 
            {
                $_SESSION['cid']=$company['id'];
                header("Location: postedjobs.php");
                exit;
            } else {
                $loginError = "Invalid email or password.";
            }
        } else {
            $loginError = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .login-btn:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }

        .forgot-password {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .forgot-password:hover
        .signup:hover {
            text-decoration: underline;
        }
        .signup{
           color: #007bff;
            text-decoration: none; 
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Company Login</h2>

    <?php if (!empty($loginError)): ?>
        <div class="error-message"><?php echo $loginError; ?></div>
    <?php endif; ?>

    <form method="POST" action="company_login.php" autocomplete="off">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required />
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required />
        </div>

        <button type="submit" class="login-btn">Login</button>
    </form>

    <a href="company_forgot_password.php" class="forgot-password">Forgot Password?</a>
    <h4>Doesn't have an accout? <a href="company_register.php" class="signup">sign up</a></h4>
</div>

</body>
</html>
