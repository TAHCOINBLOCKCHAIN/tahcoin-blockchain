<?php
header('Content-Type: application/json');

// Include database configuration
include 'config_1.php';

// Create a new database connection
$conn = getDbConnection();

// Check if the connection was successful
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Function to fetch all URLs from the database
function fetchAllUrls($conn) {
    // Query to get all URLs from the urls table
    $result = $conn->query("SELECT url, valid FROM urls ORDER BY id ASC");

    // Initialize an array to hold the URLs
    $urls = [];

    // Check if any rows were returned
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $urls[] = [
                'url' => $row['url'],
                'valid' => isset($row['valid']) ? (bool)$row['valid'] : true // Assuming valid is a boolean field
            ];
        }
    }

    return $urls; // Return the array of URLs
}

// Fetch all URLs
$urls = fetchAllUrls($conn);

// Close connection
$conn->close();

// Return the result as JSON
if (!empty($urls)) {
    echo json_encode($urls); // Return as an array of URLs
} else {
    echo json_encode(['error' => 'No URLs found.']);
}
?>