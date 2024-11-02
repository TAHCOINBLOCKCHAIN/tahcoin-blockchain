<?php
header('Content-Type: application/json');

// Include database configuration
include 'config_1.php';

// Create a new database connection
$conn = getDbConnection();

// Get the updated node statuses from the request
$updatedNodes = json_decode(file_get_contents('php://input'), true);

// Check if the updated nodes are provided
if (is_array($updatedNodes)) {
    // Prepare a statement for updating URL statuses
    $stmt = $conn->prepare("UPDATE urls SET valid = ?, timestamp = ? WHERE url = ?");

    // Loop through each updated node and update its status in the database
    foreach ($updatedNodes as $updatedNode) {
        if (isset($updatedNode['url']) && isset($updatedNode['valid'])) {
            $url = $updatedNode['url'];
            $valid = $updatedNode['valid'] ? 'true' : 'false'; // Convert boolean to string
            $timestamp = date('Y-m-d H:i:s'); // Current timestamp

            // Bind parameters and execute the statement
            $stmt->bind_param("ssi", $valid, $timestamp, $url);
            $stmt->execute();
        }
    }

    // Close the statement
    $stmt->close();

    // Output success message
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
}

// Close the database connection
$conn->close();
?>