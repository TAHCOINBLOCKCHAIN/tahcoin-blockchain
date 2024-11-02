<?php

// Include database configuration
include 'config_1.php';

// Create a new database connection
$conn = getDbConnection();

// Load URLs from the database
$urls = [];
$result = $conn->query("SELECT * FROM urls");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $urls[] = $row; // Add each URL row to the array
    }
} else {
    echo "Error loading URLs from database: " . $conn->error . "\n";
    exit;
}

// Initialize variables
$currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; // Get current page URL
$currentUrlWithoutFile = strtok($currentUrl, '?'); // Remove query string if any
$currentUrlWithoutFile = preg_replace('/\/[^\/]*$/', '', $currentUrlWithoutFile); // Remove filename from URL

// Function to verify bootnode
function verifyBootnode($bootnodeUrl) {
    $verificationFile = rtrim($bootnodeUrl, '/') . '/19990904.thr';
    $verificationResponse = @file_get_contents($verificationFile); // Suppress warnings

    if ($verificationResponse === FALSE) {
        return false; // Bootnode is offline or file not found
    }

    $verificationData = json_decode($verificationResponse, true);
    if ($verificationData !== null && 
        isset($verificationData[0]['Key']) && 
        $verificationData[0]['Key'] === 'ALHAMDULILLAH' && 
        isset($verificationData[0]['date']) && 
        $verificationData[0]['date'] == 19990904) {
        return true; // Bootnode verification successful
    }

    return false; // Verification failed
}

// Try each bootnode in the list until one works
$bootnodeFound = false;
foreach ($urls as $urlData) {
    if (isset($urlData['bootnode'])) {
        $bootnodeUrl = $urlData['url']; // Get the bootnode URL

        // Check if this bootnode is valid
        if (verifyBootnode($bootnodeUrl)) {
            // Fetch blocks from this valid bootnode
            $response = @file_get_contents(rtrim($bootnodeUrl, '/') . '/init_blocks.php'); // Suppress warnings

            if ($response !== FALSE) {
                $blocks = json_decode($response, true);
                if ($blocks !== null) {
                    // Save blocks to the database instead of a file
                    foreach ($blocks as $block) {
                        // Prepare and bind the SQL statement for inserting blocks
                        $stmt = $conn->prepare("INSERT INTO blocks (publicKey, founderKey, donateKey, blockHash, previousBlockHash, integrityHash, timestamp, transactions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        if ($stmt) {
                            // Assuming block has the following fields; adjust as necessary.
                            $stmt->bind_param(
                                "ssssssss", 
                                $block['publicKey'], 
                                $block['founderKey'], 
                                $block['donateKey'], 
                                $block['blockHash'], 
                                $block['previousBlockHash'], 
                                $block['integrityHash'], 
                                $block['timestamp'], 
                                json_encode($block['transactions']) // Assuming transactions is an array or object that can be JSON encoded
                            );

                            // Execute the prepared statement
                            if (!$stmt->execute()) {
                                echo "Error executing statement: " . $stmt->error . "\n";
                            }
                            $stmt->close();
                        } else {
                            echo "Error preparing statement: " . $conn->error . "\n";
                        }
                    }
                    echo "Blocks saved successfully from bootnode.\n";
                    $bootnodeFound = true; // Mark that we found a valid bootnode
                    break; // Exit after successful fetch from a valid bootnode
                }
            }
        }
    }
}

// If all bootnodes fail, select a random URL and fetch blocks from it
if (!$bootnodeFound && !empty($urls)) {
    // Select a random URL from the list of URLs
    $randomUrl = $urls[array_rand($urls)];

    // Construct full URL with filename for random URL
    $url = rtrim($randomUrl['url'], '/') . '/init_blocks.php';

    // Fetch blocks from random URL
    echo "Fetching blocks from random URL: {$url}\n";
    $response = @file_get_contents($url); // Suppress warnings

    // Check if response is valid
    if ($response === FALSE) {
        die('Error fetching blocks from both bootnodes and random URL.');
    }

    // Decode JSON response
    $blocks = json_decode($response, true);

    // Check if decoding was successful
    if ($blocks === null) {
        die('Error decoding JSON response from random URL.');
    }

    // Save blocks to the database instead of a file
    // Save blocks to the database instead of a file
foreach ($blocks as $block) {
    // Prepare and bind the SQL statement for inserting blocks
    $stmt = $conn->prepare("INSERT INTO blocks (publicKey, founderKey, donateKey, blockHash, previousBlockHash, integrityHash, timestamp, transactions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        // Check if transactions exist and encode accordingly
        $transactions = !empty($block['transactions']) ? json_encode($block['transactions']) : '[]'; // Use '[]' directly

        // Perform find and replace to remove quotes if transactions is "[]"
        if ($transactions === '"[]"') {
            $transactions = '[]'; // Replace with an actual empty JSON array
        }

        // Assuming block has the following fields; adjust as necessary.
        $stmt->bind_param(
            "ssssssss", 
            $block['publicKey'], 
            $block['founderKey'], 
            $block['donateKey'], 
            $block['blockHash'], 
            $block['previousBlockHash'], 
            $block['integrityHash'], 
            $block['timestamp'], 
            $transactions // Use the correctly encoded transactions
        );

        // Execute the prepared statement
        if (!$stmt->execute()) {
            echo "Error executing statement: " . $stmt->error . "\n";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error . "\n";
    }
}

    echo "Blocks saved successfully from random node.\n";
}

// Step 2: Send current URL to all nodes (including bootnodes and random nodes)
$successfulSends = 0;
$failedSends = 0;

foreach ($urls as $urlData) {
    $urlToSendTo = rtrim($urlData['url'], '/') . '/receive_url.php'; // Ensure proper formatting of URL

    // Prepare data to send
    $dataToSend = [
        "timestamp" => date(DATE_ISO8601),
        "url" => rtrim($currentUrlWithoutFile, '/'), // Ensure no trailing slashes before sending
        "valid" => true,
    ];

    // Send current URL to each node (including bootnodes)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlToSendTo);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataToSend));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    
    if ($result === FALSE) {
        $failedSends++;
        echo "Failed to send current URL to {$urlToSendTo}: " . curl_error($ch) . "\n";  // Log error message for debugging.
      } else {
        echo "Successfully sent current URL to {$urlToSendTo}.\n";  // Log success message.
        $successfulSends++;
      }

      curl_close($ch);
}

// Display summary message for sending URLs
echo "Successfully sent current URL to {$successfulSends} rivers.\n";
if ($failedSends > 0) {
   echo "Failed to send current URL to {$failedSends} rivers.\n";
}

// Close the database connection at the end of the script.
$conn->close();
?>