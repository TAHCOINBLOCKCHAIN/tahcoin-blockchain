<?php

// Include database configuration
include 'config_4.php';

// Check if the required parameters are provided
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $publicKey = $_POST['public_key'] ?? null;
    $privateKey = $_POST['private_key'] ?? null;
    $receiverAddress = $_POST['receiver_address'] ?? null;
    $amount = $_POST['amount'] ?? null;

    if (!$publicKey || !$privateKey || !$receiverAddress || !$amount) {
        echo json_encode(['error' => 'Missing required parameters.']);
        exit(1);
    }

    // Step 1: Get valid bootnode URL from the database
    $bootnodeUrl = getValidBootnodeUrl($conn);

    if ($bootnodeUrl) {
        echo "Using bootnode URL: $bootnodeUrl\n";

        // Step 2: Import Wallet
        $importResponse = sendPostRequest($bootnodeUrl . 'wallet.php', [
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
        ]);

        // Check if the import was successful
        if (strpos($importResponse, 'Invalid keys') !== false) {
            echo json_encode(['error' => 'Failed to import wallet. Invalid keys.']);
        } elseif (strpos($importResponse, 'Wallet Address:') !== false) {
            echo "Wallet imported successfully.\n";

            // Step 3: Send Transaction
            $transactionResponse = sendPostRequest($bootnodeUrl . 'wallet.php', [
                'sender' => $publicKey,
                'recipient' => $receiverAddress,
                'amount' => $amount,
            ]);

            // Extract relevant transaction details from response
            $transactionMessage = extractTransactionMessage($transactionResponse);

            // Output transaction response
            if ($transactionMessage) {
                echo json_encode(['message' => "Transaction response: " . $transactionMessage]);
            } else {
                echo json_encode(['error' => 'Failed to retrieve transaction details.']);
            }
        } else {
            echo json_encode(['error' => extractErrorMessage($importResponse)]);
        }
    } else {
        echo json_encode(['error' => 'No valid bootnodes available.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

function getValidBootnodeUrl($conn) {
    // Query to select a valid bootnode URL from the database where bootnode contains 19990904
    $result = $conn->query("SELECT url FROM urls WHERE valid = 1 AND bootnode LIKE '%19990904%' LIMIT 1");

    if ($result && $row = $result->fetch_assoc()) {
        return rtrim($row['url'], '/') . '/'; // Ensure trailing slash for URL
    }

    return null; // No valid bootnodes found
}

function sendPostRequest($url, $data) {
    // Use cURL to send POST request
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
    curl_setopt($ch, CURLOPT_POST, true); // Set method to POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Set POST fields
    
    // Execute the request and get the response
    $response = curl_exec($ch);
    
    // Check for errors
    if (curl_errno($ch)) {
        return false; // Handle cURL error appropriately in your application
    }
    
    curl_close($ch);
    
    return $response;
}

function extractTransactionMessage($html) {
    preg_match('/Transaction added\.\s*(.*?)\s*Pending Transactions/s', $html, $matches);
    
    if (isset($matches[1])) {
        return trim(strip_tags($matches[1])); // Return cleaned message without HTML tags
    }
    
    return null; // Return null if no match found
}

function extractErrorMessage($html) {
    preg_match('/Invalid keys\. Please try again\./s', $html, $matches);
    
    if (!empty($matches)) {
        return "Invalid keys. Please try again.";
    }
    
    preg_match('/<body>(.*?)<\/body>/s', $html, $bodyMatches);
    
    if (isset($bodyMatches[1])) {
        return trim(strip_tags($bodyMatches[1])); // Return cleaned message without HTML tags
    }

    return "Unknown error occurred."; // Fallback error message
}

// Close the database connection at the end of the script
$conn->close();
?>