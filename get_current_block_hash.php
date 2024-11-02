<?php
header('Content-Type: application/json');

// Include database configuration
include 'config_1.php';

// Function to fetch the latest blocks from the database
function fetchLatestBlocks($conn) {
    $result = $conn->query("SELECT * FROM blocks ORDER BY id DESC LIMIT 1");
    return $result->fetch_assoc(); // Return the latest block or null if none exist
}

// Create a new database connection
$conn = getDbConnection();

// Initialize variables to keep track of the last known state
$lastBlockHash = null;

// Infinite loop for long polling
while (true) {
    // Fetch the latest block
    $latestBlock = fetchLatestBlocks($conn);

    // Check if there is a new block
    if ($latestBlock) {
        $currentBlockHash = $latestBlock['blockHash'];

        // Check if the block hash has changed
        if ($currentBlockHash !== $lastBlockHash) {
            // Update last known block hash
            $lastBlockHash = $currentBlockHash;

            // Return the updated block data (only the latest block)
            echo json_encode($latestBlock);
            break; // Exit the loop after sending response
        }
    }

    // Sleep for a short duration before checking again
    usleep(1000000); // Sleep for 1 second (adjust as needed)
}

// Close connection
$conn->close();
?>