<?php
// Include the database connection
include('db_connect.php');

// Get the POST data (recipeId, cookbookId, userId)
$data = json_decode(file_get_contents('php://input'), true);

// Log the received data for debugging
error_log(print_r($data, true));  // Logs the incoming POST data to PHP error log

$recipeId = isset($data['recipeId']) ? $data['recipeId'] : null;
$cookbookId = isset($data['cookbookId']) ? $data['cookbookId'] : null;
$userId = isset($data['userId']) ? $data['userId'] : null;

// Check if the required data is present
if ($recipeId && $cookbookId && $userId) {
    // Prepare the query to save the recipe in the cookbook
    $query = "INSERT INTO cookbook_recipes (recipe_id, cookbook_id, user_id) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("iii", $recipeId, $cookbookId, $userId);
        $stmt->execute();

        // Check for errors in the execution of the query
        if ($stmt->affected_rows > 0) {
            echo json_encode(['message' => 'Recipe saved successfully']);
        } else {
            echo json_encode(['error' => 'Failed to save recipe. Affected rows: ' . $stmt->affected_rows]);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Failed to prepare query: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Missing data']);
}

$conn->close();
?>
