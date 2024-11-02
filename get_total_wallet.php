<?php 
include 'config_2.php'; // Ensure this line is present at the top of your script
// Create a new database connection
$conn = getDbConnection();

// Function to count unique public keys from blocks table
function countUniquePublicKeys($conn) {
    $publicKeyCount = [];

    // Fetch public keys from the blocks table
    $stmt = $conn->prepare("SELECT publicKey, founderKey, donateKey FROM blocks");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Count occurrences of publicKey, founderKey, and donateKey
        foreach (['publicKey', 'founderKey', 'donateKey'] as $key) {
            if (!empty($row[$key]) && 
                strpos($row[$key], ' ') === false && 
                preg_match('/^[a-zA-Z0-9]+$/', $row[$key])) { // Only alphanumeric characters
                
                // Increment count for this key
                if (!isset($publicKeyCount[$row[$key]])) {
                    $publicKeyCount[$row[$key]] = 0;
                }
                $publicKeyCount[$row[$key]]++;
            }
        }
    }

    return $publicKeyCount; // Return the associative array with counts
}

// Handle cURL request
header('Content-Type: application/json');

$allUniqueKeysCount = countUniquePublicKeys($conn);

// Close the database connection
$conn->close();

// Return the result as JSON
echo json_encode([
    'totalWallets' => count($allUniqueKeysCount), // Total unique public keys
    'walletCounts' => $allUniqueKeysCount // Counts of each public key
]);
?>