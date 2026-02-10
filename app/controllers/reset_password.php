<?php
session_start();
include 'db_connect.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $reset_token = $_POST['token'];

    // Validate new passwords
    if ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        // Check if the reset token exists in the database
        $query = "SELECT * FROM users WHERE reset_token = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $reset_token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the user's password
            $hashed_password = $new_password; // Securely hash the password
            $query = "UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ss', $hashed_password, $reset_token);
            $stmt->execute();


            if ($stmt->affected_rows > 0) {
                $success_message = "Your password has been updated successfully!";
            } else {
                $error_message = "Failed to update the password. Please try again.";
            }
        } else {
            $error_message = "Invalid or expired reset token!";
        }
    }
}

// Check for the token in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    $error_message = "No reset token provided!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
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

        input[type="password"] {
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        <?php if ($error_message) echo "<p class='error'>$error_message</p>"; ?>
        <?php if ($success_message) echo "<p class='success'>$success_message</p>"; ?>
        <?php if (!isset($success_message) || $success_message == ''): ?>
        <form method="POST">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" placeholder="Enter new password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>

            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">

            <button type="submit" name="reset_password">Reset Password</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
