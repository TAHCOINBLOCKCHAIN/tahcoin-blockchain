<?php
header('Content-Type: application/json');

// Include database configuration
include 'config_1.php';

// Create a new database connection
$conn = getDbConnection();

// Function to fetch total blocks count and current block count
function fetchBlockCounts($conn) {
    // Query to get the maximum ID (considered as the total number of blocks)
    $maxIdResult = $conn->query("SELECT MAX(id) AS maxId FROM blocks");
    $maxIdRow = $maxIdResult->fetch_assoc();
    
    // Get the maximum ID, which represents the total number of blocks
    $totalBlocks = (int)$maxIdRow['maxId']; // Cast to integer for safety

    // Set current block count to be the same as total blocks in this context
    $currentBlockCount = $totalBlocks;

    return [
        'totalBlocks' => $totalBlocks,
        'currentBlockCount' => $currentBlockCount
    ];
}

// Fetch block counts
$blockCounts = fetchBlockCounts($conn);

// Close connection
$conn->close();

// Return the data as JSON
echo json_encode($blockCounts);
?>