<?php
header('Content-Type: application/json');

// Enable error reporting (for development only)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
include 'config_1.php';

// Function to validate the blockchain by checking the last block in the database
function validateBlockchain($conn) {
    // Get the last block from the database
    $result = $conn->query("SELECT blockHash, previousBlockHash FROM blocks ORDER BY id DESC LIMIT 1");

    if ($result->num_rows === 0) {
        return true; // No blocks exist yet, valid to add new ones
    }

    return $result->fetch_assoc(); // Return the last block's hashes for further validation
}

// Validate incoming data from POST request
if (isset($_POST['publicKey'], $_POST['founderKey'], $_POST['donateKey'], 
          $_POST['blockHash'], $_POST['previousBlockHash'], $_POST['integrityHash'], $_POST['timestamp'])) {

    // Create a new database connection
    $conn = getDbConnection();

    // Check if connection was successful
    if (!$conn) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
        exit;
    }

    // Validate blockchain by checking previous block hash
    $lastBlock = validateBlockchain($conn);
    
    if ($lastBlock !== null && $lastBlock['blockHash'] !== $_POST['previousBlockHash']) {
        echo json_encode(['success' => false, 'error' => 'Previous block hash does not match.']);
        exit;
    }

    // Sanitize transactions input before saving
    $transactionsJson = !empty($_POST['transactions']) ? $_POST['transactions'] : '[]';

    // Decode JSON string to PHP array
    $transactionsArray = json_decode($transactionsJson, true);
    
    // Check if decoding was successful and is an array
    if (is_array($transactionsArray)) {
        foreach ($transactionsArray as &$transaction) {
            // Remove unwanted characters from each transaction string
            if (is_string($transaction)) {
                $transaction = trim($transaction, '/"');
            } elseif (is_array($transaction)) {
                foreach ($transaction as &$value) {
                    if (is_string($value)) {
                        $value = trim($value, '/"');
                    }
                }
                unset($value); // Break reference after loop
            }
        }
        unset($transaction); // Break reference after loop
        
        // Encode sanitized transactions back to JSON
        $transactionsJson = json_encode($transactionsArray);
    } else {
        $transactionsJson = json_encode([]); // Use empty array if no transactions are present.
    }

    // Prepare and bind statement for inserting into MySQL database
    try {
        // Prepare SQL statement for inserting a new block into the database.
        if ($stmt = $conn->prepare("INSERT INTO blocks (publicKey, founderKey, donateKey, blockHash, previousBlockHash, integrityHash, timestamp, transactions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
            // Bind parameters to the prepared statement.
            $stmt->bind_param("ssssssss", 
                $_POST['publicKey'], 
                $_POST['founderKey'], 
                $_POST['donateKey'], 
                $_POST['blockHash'], 
                $_POST['previousBlockHash'],
                $_POST['integrityHash'], // Use value directly from POST data
                $_POST['timestamp'], // Ensure timestamp is a string.
                $transactionsJson  // Use sanitized transactions JSON string.
            );

            // Execute statement and check for success.
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error saving block: ' . htmlspecialchars($stmt->error)]);
            }

            // Close statement.
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Prepare statement failed: ' . htmlspecialchars($conn->error)]);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'An error occurred: ' . htmlspecialchars($e->getMessage())]);
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid block data.']);
}

// Close connection if it was established
if (isset($conn) && $conn) {
    $conn->close();
}
?>