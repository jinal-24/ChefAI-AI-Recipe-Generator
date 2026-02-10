<?php
// Include database connection
include('db_connect.php');

// Get the user ID from the query parameters
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if ($user_id) {
    // Query to get cookbooks for the logged-in user
    $query = "SELECT * FROM cookbooks WHERE user_id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Fetch the cookbooks
        $cookbooks = [];
        while ($row = $result->fetch_assoc()) {
            $cookbooks[] = $row;
        }

        // Return the cookbooks as JSON
        echo json_encode($cookbooks);
    } else {
        echo json_encode(['error' => 'Failed to query cookbooks.']);
    }
} else {
    echo json_encode(['error' => 'User ID is required.']);
}
?>
