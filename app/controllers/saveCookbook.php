<?php
// Display errors to help with debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
include('db_connect.php');

// Get input data from the frontend
$data = json_decode(file_get_contents('php://input'), true);

// Check if 'name' and 'user_id' are provided and not empty
if (isset($data['name']) && !empty($data['name']) && isset($data['user_id']) && !empty($data['user_id'])) {
    $name = $data['name'];
    $user_id = $data['user_id']; // Get user_id from the request

    // Prepare and execute the query
    $query = "INSERT INTO cookbooks (name, user_id) VALUES (?, ?)";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("si", $name, $user_id); // Bind both name and user_id as parameters
        $stmt->execute();

        // Get the ID of the newly inserted cookbook
        $newCookbook = ['id' => $conn->insert_id, 'name' => $name, 'user_id' => $user_id];

        // Respond with the new cookbook as JSON
        echo json_encode($newCookbook);
    } else {
        // Error preparing the statement
        echo json_encode(['error' => 'Failed to prepare statement']);
    }
} else {
    // Missing or empty name or user_id
    echo json_encode(['error' => 'Cookbook name and user ID are required']);
}
?>
