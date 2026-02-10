<?php
session_start();
include 'db_connect.php';
require 'vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Old hashed password check
        // if (password_verify($password, $user['password'])) {

        // New plain-text password check
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $success_message = "Sign In successful! Redirecting...";
            echo "<script>
                setTimeout(() => {
                    window.location.href = 'generate1.php';
                }, 2000);
            </script>";
        } else {
            $error_message = "Invalid username or password!";
        }
    } else {
        $error_message = "Invalid username or password!";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['forgot_password'])) {
    $email = $_POST['email'];

    // Check if email exists
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $reset_token = bin2hex(random_bytes(32));
        $query = "UPDATE users SET reset_token = ? WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $reset_token, $email);
        $stmt->execute();

        // Send reset email using PHPMailer
        $reset_link = "http://localhost/ChefAI/reset_password.php?token=$reset_token";
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'johnbloom0405@gmail.com'; // Your Gmail address
            $mail->Password = 'xigl ssvt yjzs zkve'; // Your Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('johnbloom0405@gmail.com', 'ChefAI'); // Your email and sender name
            $mail->addAddress($email);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                Hi,<br><br>
                Click the link below to reset your password:<br><br>
                <a href='$reset_link'>$reset_link</a><br><br>
                If you did not request this, please ignore this email.
            ";

            $mail->send();
            $success_message = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $error_message = "Failed to send reset email. Error: {$mail->ErrorInfo}";
        }
    } else {
        $error_message = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="icon" href="favicon.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #555555;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #dddddd;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #cc9c54;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: #b17f3c;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        p {
            text-align: center;
        }

        p a {
            color: #cc9c54;
            text-decoration: none;
            font-weight: bold;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
        <h1>Sign In</h1>
        <?php 
        if ($error_message) echo "<p class='error'>$error_message</p>";
        if (isset($success_message)) echo "<p class='success'>$success_message</p>";
        ?>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Enter your username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit" name="login">Sign In</button>
        </form>
        <p><a href="#" onclick="document.getElementById('forgotPasswordForm').style.display='block'; return false;">Forgot Password?</a></p>
        <div id="forgotPasswordForm" style="display: none;">
            <form method="POST">
                <label for="email">Enter your email:</label>
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit" name="forgot_password">Send Reset Link</button>
            </form>
        </div>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>

