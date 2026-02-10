<?php
session_start(); // Start the session

// Check if the user is logged in (check if session variable exists)
if (isset($_SESSION['user_id'])) {
    // If logged in, return the user_id as JSON
    echo json_encode(['user_id' => $_SESSION['user_id']]);
} else {
    // If not logged in, return an error
    echo json_encode(['error' => 'User is not logged in']);
}
?>
