<?php
session_start();

include 'connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

function generateOTP($length = 6) {
    return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

function sendOTPEmail($email, $otp, &$errorMsg = '') {
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'appasivaishnavi@gmail.com';    
        $mail->Password   = 'sphy yopl txfr apnn';           
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
        $mail->Port       = 587;

        $mail->setFrom('appasivaishnavi@gmail.com', 'Campus System');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Password Reset';
        $mail->Body    = "<h3>Your OTP is: <b>$otp</b></h3><p>It is valid for 5 minutes.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        $errorMsg = $mail->ErrorInfo;
        return false;
    }
}

$show_otp_form = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_otp'])) {
        $email = $_POST['email'];

        $stmt = $con->prepare("SELECT id FROM student WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $otp = generateOTP();
            $expires = time() + 300; 
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expires'] = $expires;
            $_SESSION['otp_email'] = $email;

            $errorMsg = '';
            if (sendOTPEmail($email, $otp, $errorMsg)) {
                $message = "<p class='success'>OTP sent to $email</p>";
                $show_otp_form = true;
            } else {
                $message = "<p class='error'>Failed to send OTP email. Error: $errorMsg</p>";
            }
        } else {
            $message = "<p class='error'>Email not found.</p>";
        }
    } elseif (isset($_POST['verify_otp'])) {
        $email = $_POST['email'];
        $otp = $_POST['otp'];
        $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        if (
            isset($_SESSION['otp'], $_SESSION['otp_expires'], $_SESSION['otp_email']) &&
            $_SESSION['otp_email'] === $email &&
            $_SESSION['otp'] === $otp &&
            time() <= $_SESSION['otp_expires']
        ) {
            $stmt = $con->prepare("UPDATE student SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $new_pass, $email);
            if ($stmt->execute()) {
                $message = "<p class='success'>Password reset successful.</p>";

                unset($_SESSION['otp'], $_SESSION['otp_expires'], $_SESSION['otp_email']);
                $show_otp_form = false;
                header("refresh:2; url=student_login.php");
            } else {
                $message = "<p class='error'>Failed to update password. Please try again.</p>";
                $show_otp_form = true;
            }
        } else {
            $message = "<p class='error'>Invalid or expired OTP.</p>";
            $show_otp_form = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 30px;
            width: 350px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        form input[type="email"],
        form input[type="text"],
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #2e86de;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #1b4f72;
        }
        .success {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>
    <?php if ($message) echo $message; ?>

    <?php if (!$show_otp_form): ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="send_otp">Send OTP</button>
        </form>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <input type="password" name="new_password" placeholder="Enter New Password" required>
            <button type="submit" name="verify_otp">Reset Password</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
