<?php
session_start();
include 'db_connect.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email address!";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        $check_query = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param('ss', $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email or username already exists!";
        } else {
            $insert_query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param('sss', $username, $email, $password);

            if ($stmt->execute()) {
                $success_message = "Registration successful! You can now log in.";
            } else {
                $error_message = "Registration failed! Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="icon" href="favicon.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
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
            width: 96%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #dddddd;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            width: 99%;
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
    <?php 
        if ($error_message) echo "<p class='error'>$error_message</p>";
        if (isset($success_message)) echo "<p class='success'>$success_message</p>";
    ?>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Create a username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Enter your email address" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Create a password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" placeholder="Re-enter your password" required>

            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Sign In here</a></p>
    </div>
</body>
</html>
