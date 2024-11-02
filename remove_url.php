<?php

// Include database configuration
include 'config_1.php';

// Create a new database connection
$conn = getDbConnection();

// Remove invalid URLs (assuming 'valid' is 0 for invalid URLs)
$removeInvalidSql = "DELETE FROM urls WHERE valid = 0";
if ($conn->query($removeInvalidSql) === TRUE) {
    echo "Invalid URLs removed successfully.<br>";
} else {
    echo "Error removing invalid URLs: " . $conn->error . "<br>";
}

// Remove duplicate URLs while keeping one instance
// First, create a temporary table to store unique URLs
$tempTableSql = "CREATE TEMPORARY TABLE temp_urls AS 
                 SELECT MIN(id) AS id, url 
                 FROM urls 
                 GROUP BY url";

if ($conn->query($tempTableSql) === TRUE) {
    // Now delete all records from the original table
    $deleteDuplicatesSql = "DELETE FROM urls";
    if ($conn->query($deleteDuplicatesSql) === TRUE) {
        // Insert back the unique URLs from the temporary table
        $insertUniqueSql = "INSERT INTO urls (id, url, timestamp, valid)
                            SELECT id, url, timestamp, valid FROM temp_urls";

        if ($conn->query($insertUniqueSql) === TRUE) {
            echo "Duplicate URLs removed successfully.<br>";
        } else {
            echo "Error inserting unique URLs back: " . $conn->error . "<br>";
        }
    } else {
        echo "Error deleting duplicates: " . $conn->error . "<br>";
    }
} else {
    echo "Error creating temporary table: " . $conn->error . "<br>";
}

// Close the database connection
$conn->close();
?>