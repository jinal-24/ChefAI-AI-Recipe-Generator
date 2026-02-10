<?php
// Database connection
include('db_connect.php');

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$newName = $data['newName'];

$query = "UPDATE cookbooks SET name = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $newName, $id);
$stmt->execute();

echo json_encode(['status' => 'success']);
?>
