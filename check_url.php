<?php
header('Content-Type: application/json');

// Include database configuration
include 'config_1.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$url = $input['url'] ?? '';

$conn = getDbConnection();

// Prepare statement to check if URL exists
$stmt = $conn->prepare("SELECT COUNT(*) FROM urls WHERE url = ?");
$stmt->bind_param("s", $url);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

// Return response
echo json_encode(['exists' => $count > 0]);

// Close connection
$conn->close();
?>