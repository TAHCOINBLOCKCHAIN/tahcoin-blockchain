<?php

// Include database configuration
include 'config_1.php';

// Create a new database connection
$conn = getDbConnection();

// Get POST data from incoming request
$data = json_decode(file_get_contents('php://input'), true);

// Validate received data before adding it to the database
if (isset($data['timestamp'], $data['url'], $data['valid'])) {
    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("SELECT COUNT(*) FROM urls WHERE url = ?");
    $urlWithoutTrailingSlash = rtrim($data['url'], '/'); // Ensure no trailing slashes when checking for duplicates.
    
    // Bind parameters and execute the statement
    $stmt->bind_param("s", $urlWithoutTrailingSlash);
    $stmt->execute();
    
    // Get the result
    $stmt->bind_result($urlExistsCount);
    $stmt->fetch();
    
    // Close the prepared statement for checking existence
    $stmt->close();

    // If the URL does not exist, insert it into the database
    if ($urlExistsCount == 0) {
        // Prepare the insert statement
        $stmt = $conn->prepare("INSERT INTO urls (timestamp, url, valid) VALUES (?, ?, ?)");
        
        // Bind parameters
        $stmt->bind_param("ssi", $data['timestamp'], $urlWithoutTrailingSlash, $data['valid']);
        
        // Execute the insert statement
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "URL added successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add URL."]);
        }
        
        // Close the prepared statement for insertion
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "URL already exists."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid data received."]);
}

// Close the database connection
$conn->close();
?>