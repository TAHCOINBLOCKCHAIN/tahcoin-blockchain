<?php
header('Content-Type: application/json');

// Include database configuration
include 'config_1.php';

// Get the JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Add the new block
$publicKey = $input['publicKey'] ?? '';
$founderKey = $input['founderKey'] ?? '';
$donateKey = $input['donateKey'] ?? '';
$blockHash = $input['blockHash'] ?? '';
$previousBlockHash = $input['previousBlockHash'] ?? '';
$integrityHash = $input['integrityHash'] ?? '';
$timestamp = $input['timestamp'] ?? 0;
$transactions = json_encode($input['transactions'] ?? []); // Encode transactions as JSON

// Create a new database connection
$conn = getDbConnection();

if ($publicKey && $blockHash) {
    // Check if there are existing blocks to validate against
    $result = $conn->query("SELECT blockHash FROM blocks ORDER BY id DESC LIMIT 1");
    
    if ($result->num_rows > 0) {
        // Get the last block's hash
        $lastBlock = $result->fetch_assoc();
        $lastBlockHash = $lastBlock['blockHash'];
        
        // Validate previous block hash
        if ($previousBlockHash !== $lastBlockHash) {
            echo json_encode(['success' => false, 'error' => 'Previous block hash does not match the last block.']);
            exit;
        }
    }

    // Create the new block without encryption
    $newBlock = [
        'publicKey' => $publicKey, 
        'founderKey' => $founderKey, 
        'donateKey' => $donateKey, 
        'blockHash' => $blockHash, 
        'previousBlockHash' => $previousBlockHash, 
        'integrityHash' => $integrityHash, 
        'timestamp' => strval($timestamp), 
        'transactions' => $transactions 
    ];

    // Prepare and bind statement for inserting into MySQL database
    $stmt = $conn->prepare("INSERT INTO blocks (publicKey, founderKey, donateKey, blockHash, previousBlockHash, integrityHash, timestamp, transactions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("ssssssss", 
            $newBlock['publicKey'], 
            $newBlock['founderKey'], 
            $newBlock['donateKey'], 
            $newBlock['blockHash'], 
            $newBlock['previousBlockHash'],
            $newBlock['integrityHash'], 
            $newBlock['timestamp'], 
            $newBlock['transactions']
        );

        // Attempt to execute the statement
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Block added successfully.']);
            
            // Check if the last block has transactions after saving the new block
            checkAndClearPendingTransactions($conn);
            
        } else {
            // Log error message related to saving block in console instead of UI.
            error_log('Error saving block: ' . htmlspecialchars($stmt->error));
            echo json_encode(['success' => false, 'error' => 'Error saving block: ' . htmlspecialchars($stmt->error)]);
        }
        
        // Close statement
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Prepare statement failed: ' . htmlspecialchars($conn->error)]);
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input.']);
}

// Function to check last block's transactions and clear pending ones if necessary
function checkAndClearPendingTransactions($conn) {
    // Get the last block from the database
    $result = $conn->query("SELECT transactions FROM blocks ORDER BY id DESC LIMIT 1");
    
    if ($result && $result->num_rows > 0) {
        // Fetch the last block's transactions
        $lastBlock = $result->fetch_assoc();
        
        // Decode transactions from JSON format
        $transactions = json_decode($lastBlock['transactions'], true);
        
        // Check if there are any transactions in the last block
        if (!empty($transactions)) {
            clearPendingTransactions(); // Clear pending transactions only if there are transactions in the last block
        }
    }
}

// Function to clear pending transactions from file
function clearPendingTransactions() {
    // Assuming your pending transactions are stored in a JSON file called "pending_transactions.json"
    file_put_contents('pending_transactions.thr', json_encode([])); // Clear the file by writing an empty array
}

// Close connection
$conn->close();
?>