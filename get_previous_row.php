<?php
// Include database configuration
include 'config_1.php'; // Ensure this file has the function getDbConnection()

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Create a new database connection
    $conn = getDbConnection();

    // Fetch the latest block (previous block)
    $stmt = $conn->query("SELECT * FROM blocks ORDER BY id DESC LIMIT 1");
    $previousRow = $stmt->fetch_assoc(); // Use fetch_assoc() for mysqli

    if ($previousRow) {
        // Convert previous row data to JSON string
        $rowDataString = json_encode($previousRow);
        
        // Hash the previous row's data using SHA-256 in PHP
        $rowHash = hash('sha256', $rowDataString);

        // Return the hash and previous row data as a JSON response
        echo json_encode([
            'success' => true,
            'rowHash' => $rowHash,
            'previousRow' => $previousRow
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No previous row found.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    // Close the database connection
    if (isset($conn)) {
        $conn->close();
    }
}
?>