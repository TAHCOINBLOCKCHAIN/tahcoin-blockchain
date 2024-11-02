<?php
include 'config_1.php'; // Include the database configuration

// Function to calculate SHA-256 hash of a block
function calculateHash($data) {
    return hash('sha256', json_encode($data));
}

// Function to validate the blockchain by checking from last block to first
function validateBlockchain($blocks) {
    // Start from the last block and move backwards
    for ($i = count($blocks) - 1; $i > 0; $i--) {
        // Check if the integrity hash matches the hash of the previous block
        if ($blocks[$i]['integrityHash'] !== calculateHash($blocks[$i - 1])) {
            // Return the altered block's data
            return [
                'valid' => false,
                'alteredBlock' => $blocks[$i]
            ];
        }
        
        // Check if the current block's integrity matches its successor's integrity
        if ($i < count($blocks) - 1) { // Ensure we're not at the last block
            if ($blocks[$i + 1]['integrityHash'] !== calculateHash($blocks[$i])) {
                return [
                    'valid' => false,
                    'alteredBlock' => $blocks[$i]
                ];
            }
        }
    }

    return ['valid' => true];
}

// Function to fetch blocks from the database
function fetchBlocks() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM blocks ORDER BY id"; // Assuming there's an 'id' field for ordering
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $blocks = [];
        while ($row = $result->fetch_assoc()) {
            // Add each row's data directly without decoding
            $blocks[] = $row;
        }
        return $blocks;
    } else {
        return [];
    }
}

// Fetch blocks from the database
$blocks = fetchBlocks();

// Check if any blocks were found
if (empty($blocks)) {
    echo json_encode(['success' => false, 'message' => 'No blocks found in the blockchain.']);
} else {
    // Validate the blockchain
    $validationResult = validateBlockchain($blocks);
    
    if ($validationResult['valid']) {
        echo json_encode(['success' => true, 'message' => 'Blockchain is valid and not altered.']);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Blockchain is altered.',
            'alteredBlock' => $validationResult['alteredBlock']
        ]);
    }
}
?>