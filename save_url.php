<?php
header('Content-Type: application/json');

// Include database configuration
include 'config_1.php';

// Create a new database connection
$conn = getDbConnection();

// Get the JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Check if the URL is provided
if (isset($input['url'])) {
    $newUrl = $input['url'];

    // Check if the URL already exists in the database
    $stmt = $conn->prepare("SELECT COUNT(*) FROM urls WHERE url = ?");
    $stmt->bind_param("s", $newUrl);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        // Prepare SQL statement to insert the new URL
        $timestamp = date('Y-m-d H:i:s'); // Current timestamp
        $valid = 'true'; // Set validity status (can be adjusted as needed)

        $stmt = $conn->prepare("INSERT INTO urls (url, timestamp, valid) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $newUrl, $timestamp, $valid);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'URL saved successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save URL.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'URL already exists.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No URL provided.']);
}

// Close the database connection
$conn->close();
?>