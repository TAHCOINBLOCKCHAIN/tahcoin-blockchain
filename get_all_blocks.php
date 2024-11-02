<?php
include 'config_1.php'; // Include the database configuration

// Function to fetch all blocks from the database
function fetchAllBlocks() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM blocks ORDER BY id"; // Assuming there's an 'id' field for ordering
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $blocks = [];
        while ($row = $result->fetch_assoc()) {
            $blocks[] = $row; // Directly add each block's data without decoding
        }
        return $blocks;
    } else {
        return [];
    }
}

// Check if the script is called from CLI or via HTTP
if (php_sapi_name() == "cli") {
    // CLI mode
    $blocks = fetchAllBlocks();
    
    if (empty($blocks)) {
        echo "No blocks found in the blockchain.\n";
    } else {
        foreach ($blocks as $block) {
            echo json_encode($block, JSON_PRETTY_PRINT) . "\n";
        }
    }
} else {
    // HTTP mode
    header('Content-Type: application/json');
    
    $blocks = fetchAllBlocks();
    
    if (empty($blocks)) {
        echo json_encode(['success' => false, 'message' => 'No blocks found in the blockchain.']);
    } else {
        echo json_encode(['success' => true, 'data' => $blocks]);
    }
}
?>