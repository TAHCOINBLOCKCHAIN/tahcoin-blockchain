<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database configuration
include 'config_1.php';

// Create a new database connection
$conn = getDbConnection();
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit; // Exit if the connection fails
}

// Get the updated URLs from the request
$updatedUrls = json_decode(file_get_contents('php://input'), true);

// Check if the updated URLs are provided
if (!is_array($updatedUrls)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input format.']);
    exit;
}

// Prepare a statement for inserting/updating URLs
$stmt = $conn->prepare("INSERT INTO urls (url, timestamp, valid) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE timestamp = VALUES(timestamp), valid = VALUES(valid)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Statement preparation failed: ' . $conn->error]);
    exit;
}

// Loop through each URL and save it to the database
foreach ($updatedUrls as $urlData) {
    if (isset($urlData['url'])) {
        // Get the URL and ensure it uses HTTPS
        $url = $urlData['url'];

        // Force HTTPS protocol
        if (stripos($url, 'http://') === 0) {
            $url = 'https://' . substr($url, 7); // Replace http:// with https://
        } elseif (stripos($url, 'https://') !== 0) {
            $url = 'https://' . $url; // Add https:// if no protocol is present
        }

        $timestamp = date('Y-m-d H:i:s'); // Current timestamp
        $valid = isset($urlData['valid']) ? (bool)$urlData['valid'] : true; // Use provided validity or default to true

        // Bind parameters and execute the statement
        $stmt->bind_param("ssi", $url, $timestamp, $valid);
        
        // Check for execution errors
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Database query failed: ' . $stmt->error]);
            exit;
        }
    }
}

// Close the statement and database connection
$stmt->close();
$conn->close();

// Output success message
echo json_encode(['success' => true]);
?>