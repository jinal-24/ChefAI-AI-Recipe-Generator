<?php
// Include database connection file
include('db_connection.php');

// Get cookbook ID from the request
$cookbookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Initialize response array
$response = [];

// Validate if cookbook ID is provided
if ($cookbookId <= 0) {
    // If no valid ID is provided, return an error
    $response['error'] = 'Invalid cookbook ID';
    echo json_encode($response);
    exit;
}

try {
    // Fetch cookbook details from the database
    $stmt = $pdo->prepare("SELECT * FROM cookbooks WHERE id = ?");
    $stmt->execute([$cookbookId]);
    $cookbook = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the cookbook exists
    if ($cookbook) {
        // Fetch recipes associated with this cookbook
        $stmt = $pdo->prepare("SELECT id, name, details, image_url FROM recipes WHERE cookbook_id = ?");
        $stmt->execute([$cookbookId]);
        $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare the response
        $response['name'] = $cookbook['name'];
        $response['recipes'] = $recipes;
    } else {
        // If cookbook doesn't exist
        $response['error'] = 'Cookbook not found';
    }
} catch (PDOException $e) {
    // Handle any database errors
    $response['error'] = 'Database error: ' . $e->getMessage();
}

// Return the response as JSON
echo json_encode($response);
?>
