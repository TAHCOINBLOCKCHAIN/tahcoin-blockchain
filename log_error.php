<?php

// Set the content type to JSON
header('Content-Type: application/json');

// Path to the error log file
$errorLogFile = 'error_logs.json';

// Get the incoming request data
$data = json_decode(file_get_contents('php://input'), true);

// Check if the error message is set
if (isset($data['error'])) {
    $errorMessage = $data['error'];
    
    // Create a timestamp for the error log entry
    $timestamp = date('Y-m-d H:i:s');
    
    // Prepare the error entry
    $errorEntry = [
        "timestamp" => $timestamp,
        "message" => $errorMessage,
    ];

    // Load existing logs or initialize an empty array
    if (file_exists($errorLogFile)) {
        $existingLogs = json_decode(file_get_contents($errorLogFile), true);
    } else {
        $existingLogs = [];
    }

    // Append new error entry
    $existingLogs[] = $errorEntry;

    // Save updated logs back to error_logs.json in pretty format
    file_put_contents($errorLogFile, json_encode($existingLogs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    echo json_encode(["status" => "success", "message" => "Error logged successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid data received."]);
}
?>