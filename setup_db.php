<?php
// Include database configuration
include 'config_1.php';

// Create a new database connection
$conn = getDbConnection();

// SQL to create blocks table
$sqlBlocks = "CREATE TABLE IF NOT EXISTS blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    publicKey VARCHAR(255),
    founderKey VARCHAR(255),
    donateKey VARCHAR(255),
    blockHash VARCHAR(255) UNIQUE,
    previousBlockHash VARCHAR(255),
    integrityHash VARCHAR(255) UNIQUE,
    timestamp VARCHAR(255) UNIQUE,  -- Keeping timestamp as VARCHAR(255)
    transactions JSON
);";

// SQL to create urls table
$sqlUrls = "CREATE TABLE IF NOT EXISTS urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL UNIQUE,  -- Added UNIQUE constraint to prevent duplicate URLs
    bootnode VARCHAR(255),  -- Keeping bootnode as VARCHAR(255)
    timestamp VARCHAR(255),  -- Keeping timestamp as VARCHAR(255)
    valid VARCHAR(255) DEFAULT '1'  -- Keeping valid as VARCHAR(255) for consistency
);";

// Execute the queries and check for errors
if ($conn->query($sqlBlocks) === TRUE) {
    echo "Table 'blocks' created successfully.<br>";
} else {
    echo "Error creating table 'blocks': " . $conn->error . "<br>";
}

if ($conn->query($sqlUrls) === TRUE) {
    echo "Table 'urls' created successfully.<br>";
} else {
    echo "Error creating table 'urls': " . $conn->error . "<br>";
}

// Prepare to insert new URL entry into the urls table
$timestamp = "2024-09-23T21:35:25.337Z";
$url = "https://tahriver.online/";
$bootnode = "19990904";
$valid = true;

// Check if the URL already exists to prevent duplicates
$stmt = $conn->prepare("SELECT COUNT(*) FROM urls WHERE url = ?");
$stmt->bind_param("s", $url);
$stmt->execute();
$stmt->bind_result($urlExistsCount);
$stmt->fetch();
$stmt->close();

if ($urlExistsCount == 0) {
    // Insert the new URL into the urls table
    $stmt = $conn->prepare("INSERT INTO urls (timestamp, url, valid) VALUES (?, ?, ?)");
    
    // Convert boolean to string for consistency with your schema
    $validString = $valid ? '1' : '0'; 

    if ($stmt) {
        $stmt->bind_param("ssi", $timestamp, $url, $validString);
        if ($stmt->execute()) {
            echo "New URL added successfully.<br>";
        } else {
            echo "Error adding new URL: " . $stmt->error . "<br>";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement for URL insertion: " . $conn->error . "<br>";
    }
} else {
    echo "URL already exists in the database.<br>";
}

// Close connection
$conn->close();
?>