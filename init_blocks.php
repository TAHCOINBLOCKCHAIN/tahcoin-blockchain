<?php
header('Content-Type: application/json');

// Include database configuration
include 'config_1.php';

// Create a new database connection
$conn = getDbConnection();

// Prepare and execute the SQL query to fetch blocks
$sql = "SELECT * FROM blocks"; // Adjust the query if you need specific columns
$result = $conn->query($sql);

// Check if any records were returned
if ($result) {
    $blocksData = [];
    
    while ($row = $result->fetch_assoc()) {
        $blocksData[] = $row; // Add each block row to the array
    }

    // Return the blocks data as JSON
    echo json_encode($blocksData);
} else {
    // Handle query error
    echo json_encode(["status" => "error", "message" => "Error fetching blocks: " . $conn->error]);
}

// Close the database connection
$conn->close();
?>