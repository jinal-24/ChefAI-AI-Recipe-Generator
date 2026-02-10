<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

// Retrieve POST data
$name = trim($_POST['name'] ?? '');
if (empty($name)) {
    die("Error: Cookbook name is required.");
}

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=chef", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Error: Could not connect to the database.");
}

$user_id = $_SESSION['user_id'];

// Insert query
$query = "INSERT INTO cookbooks (user_id, name) VALUES (?, ?)";
$stmt = $pdo->prepare($query);

if ($stmt->execute([$user_id, $name])) {
    echo "Cookbook added successfully!";
} else {
    echo "Error: Could not add cookbook.";
}
?>
