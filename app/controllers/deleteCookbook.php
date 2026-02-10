<?php
// Database connection
include('db_connect.php');

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

$query = "DELETE FROM cookbooks WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(['status' => 'success']);
?>
