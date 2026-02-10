<?php
session_start();

// Connect to a database (MySQL)
$host = "localhost";
$dbname = "chef";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle Login/Register
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $action = $_POST["action"]; // 'login' or 'register'

    if ($action === "register") {
        // Register new user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            echo "Username already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed_password]);
            $_SESSION["username"] = $username;
            header("Location: index.html"); // Redirect to homepage
        }
    } elseif ($action === "login") {
        // Login existing user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["username"] = $username;
            header("Location: index.html"); // Redirect to homepage
        } else {
            echo "Invalid username or password.";
        }
    }
}
?>
