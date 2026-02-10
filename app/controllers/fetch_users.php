<?php
include 'db_connect.php'; // Use the existing PDO connection

$sql = "SELECT * FROM users";
$stmt = $pdo->query($sql);

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row["id"] . " - Username: " . $row["username"] . "<br>";
    }
} else {
    echo "No users found!";
}
?>
