<?php
// Include database configuration
include 'config_1.php'; 

// Function to fetch the latest block header and previous row from the database
function getLatestBlockHeaderAndPreviousRow($conn) {
    // Query to get the last two rows from the blocks table
    $query = "SELECT * FROM blocks ORDER BY id DESC LIMIT 2"; // Adjust table/column names as needed
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        
        // If we have at least two rows, treat the second row as the previous row and first as the latest
        if (count($rows) == 2) {
            return [
                'latestBlockHeader' => $rows[0], // Latest block
                'previousRow' => $rows[1]         // Previous row
            ];
        } elseif (count($rows) == 1) {
            // Only one row exists, treat it as both latest and previous (if applicable)
            return [
                'latestBlockHeader' => $rows[0],
                'previousRow' => null // No previous row available
            ];
        }
    }
    
    return null; // Return null if no rows found
}

// Main execution
$conn = getDbConnection();
$data = getLatestBlockHeaderAndPreviousRow($conn);

if ($data !== null) {
    echo json_encode([
        'latestBlockHeader' => $data['latestBlockHeader'],
        'previousRow' => $data['previousRow']
    ]); // Return as JSON response
} else {
    http_response_code(404); // Not found response code if no headers are found
    echo json_encode(['error' => 'No block headers found.']);
}

$conn->close();
?>