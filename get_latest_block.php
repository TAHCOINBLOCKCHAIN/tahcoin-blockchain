<?php
// Include database configuration
include 'config_1.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [];

// Query to get the latest block
$query = "SELECT * FROM blocks ORDER BY id DESC LIMIT 1"; // Adjust 'id' to your primary key or ordering field

// Execute the query
$result = $conn->query($query);

// Check if the query was successful
if ($result) {
    // Fetch the latest block as an associative array
    if ($latestBlock = $result->fetch_assoc()) {
        // Return the latest block as JSON
        $response = [
            "success" => true,
            "block" => $latestBlock // Assuming this contains all necessary block data
        ];
    } else {
        // No blocks found
        $response = [
            "success" => false,
            "error" => "No blocks found."
        ];
    }
} else {
    // Query failed
    $response = [
        "success" => false,
        "error" => "Database query failed: " . $conn->error
    ];
}

// Close the database connection
$conn->close();

// Return JSON response
echo json_encode($response);
?>