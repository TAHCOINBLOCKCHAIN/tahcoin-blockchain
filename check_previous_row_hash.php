<?php
// Include database configuration
include 'config_1.php'; // Ensure this file has the function getDbConnection()

// Set content type to HTML for a more readable output
header('Content-Type: text/html');

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

        // Display the results
        echo "<h1>Previous Row Hash Debug</h1>";
        echo "<h2>Previous Row Data:</h2>";
        echo "<pre>" . htmlspecialchars($rowDataString) . "</pre>"; // Display JSON safely
        echo "<h2>Generated Hash:</h2>";
        echo "<pre>" . htmlspecialchars($rowHash) . "</pre>"; // Display hash safely
    } else {
        echo "<h1>No Previous Row Found</h1>";
    }
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
} finally {
    // Close the database connection
    if (isset($conn)) {
        $conn->close();
    }
}
?>