<?php
// Database connection
$host = 'localhost';
$user = 'root'; // Replace with your database username
$pass = '';     // Replace with your database password
$db = 'chef';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

// Fetch the recipe name from the GET request
if (isset($_GET['recipe'])) {
    $recipe = $conn->real_escape_string($_GET['recipe']);
    $query = "SELECT url FROM recipe_table WHERE recipe_name LIKE '%$recipe%' LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'url' => $row['url']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Recipe not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>
