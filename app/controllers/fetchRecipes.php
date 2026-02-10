<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chef";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get cookbook ID from query parameters
$cookbook_id = isset($_GET['cookbook_id']) ? intval($_GET['cookbook_id']) : 0;

// Fetch cookbook name
$sql_cookbook = "SELECT name FROM cookbooks WHERE id = ?";
$stmt_cookbook = $conn->prepare($sql_cookbook);
$stmt_cookbook->bind_param("i", $cookbook_id);
$stmt_cookbook->execute();
$result_cookbook = $stmt_cookbook->get_result();

$cookbook_name = "";
if ($row = $result_cookbook->fetch_assoc()) {
    $cookbook_name = $row['name'];
}
$stmt_cookbook->close();

// Fetch recipes
$sql = "SELECT r.recipe_name, r.url
        FROM recipe_table r
        INNER JOIN cookbook_recipes cr ON r.recipe_id = cr.recipe_id
        WHERE cr.cookbook_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Prepare Error: " . $conn->error);
}

$stmt->bind_param("i", $cookbook_id);
$stmt->execute();
$result = $stmt->get_result();

$recipes = [];
while ($row = $result->fetch_assoc()) {
    $recipes[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode([
    "success" => true,
    "cookbook_name" => $cookbook_name,
    "recipes" => $recipes
]);
?>
