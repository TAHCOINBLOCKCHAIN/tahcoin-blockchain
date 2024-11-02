<?php
// Include database configuration
include 'config.php'; // Ensure this file has the function getDbConnection()

// Function to generate SHA-256 hash
function generateSHA256($blockHeader, $nonce) {
    $headerWithNonce = $blockHeader . ";nonce:" . $nonce; // Append nonce to block header
    return hash('sha256', $headerWithNonce); // Generate SHA-256 hash
}

// Function to fetch the latest block header from the database
function getLatestBlockHeader($conn) {
    $query = "SELECT block_header FROM blocks ORDER BY id DESC LIMIT 1"; // Adjust table/column names as needed
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['block_header']; // Return block header from the latest row
    }
    
    return null; // Return null if no block header found
}

// Get parameters from the request
if (isset($_GET['nonce'])) {
    $nonce = intval($_GET['nonce']); // Convert nonce to integer

    // Create a new database connection
    $conn = getDbConnection();

    // Fetch the latest block header from the database
    $blockHeader = getLatestBlockHeader($conn);

    if ($blockHeader !== null) {
        // Generate hash using the retrieved block header and nonce
        $hash = generateSHA256($blockHeader, $nonce);
        
        // Return the hash as a JSON response
        echo json_encode(['hash' => $hash]);
    } else {
        // Return an error if no block header is found
        http_response_code(404);
        echo json_encode(['error' => 'No previous block header found.']);
    }

    $conn->close(); // Close database connection
} else {
    // Return an error if parameters are missing
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
}
?>