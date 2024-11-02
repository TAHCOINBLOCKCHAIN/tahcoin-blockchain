<?php
// Include database configuration
include 'config_2.php'; // Ensure this line is present at the top of your script

// Create a new database connection
$conn = getDbConnection();
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Set the content type to JSON
header('Content-Type: application/json');

// Initialize variables
$walletAddress = '';
$totalSent = 0;
$totalReceived = 0;
$totalBlocksFound = 0;
$currentTahcoin = 0;
$pendingRewards = 0;
$pendingReceived = 0; // Initialize pending received variable
$defaultReward = 0.019990904; // Default reward per block

// Array to hold all transactions for the wallet address
$allTransactions = [];

// Process the wallet import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['publicKey'])) {
    $walletAddress = $_POST['publicKey'];

    // Load balances data
    $balancesFile = 'balances.thr';

    if (!file_exists($balancesFile)) {
        echo json_encode(['error' => 'Balances file not found.']);
        exit;
    }

    $balancesData = json_decode(file_get_contents($balancesFile), true);
    
    // Store the public key in memory
    $publicKey = $walletAddress; // Store the original public key for calculations

    // Process each block and its transactions using the generator
    foreach (getBlocksWithTransactions($conn) as $item) {
        // Check if the item is a block or a transaction
        if (isset($item['transactions'])) {
            // This is a block
            $blockIndex = intval($item['id']); // Using block ID as the index

            // Iterate through transactions yielded from the generator
            foreach (getTransactionsFromBlock($item) as $transaction) {
                // Assuming transaction details are stored in plain text
                $sender = $transaction['sender']; // No decryption needed
                $recipient = $transaction['recipient']; // No decryption needed
                $amount = floatval($transaction['amount']); // Convert amount to float

                // Collect transactions related to this public key
                if ($sender === $publicKey || $recipient === $publicKey) {
                    // Attach block hash and timestamp to transaction details
                    $transaction['blockHash'] = htmlspecialchars($item['blockHash']);
                    $transaction['timestamp'] = date('Y-m-d H:i:s', intval($item['timestamp'] / 1000)); // Format timestamp

                    // Store each transaction in the array
                    $allTransactions[] = $transaction;

                    // Check if the transaction sender matches the public key
                    if ($sender === $publicKey) {
                        $totalSent += $amount; // Accumulate total sent amount
                    }
                    // Check if the transaction recipient matches the public key
                    if ($recipient === $publicKey) {
                        // Check for 199 block confirmations before adding to totalReceived
                        if ($blockIndex >= 199) {  // Confirmed after 199 blocks
                            $totalReceived += $amount; // Accumulate total received amount
                        } else {
                            $pendingReceived += $amount; // Accumulate pending received amount
                        }
                    }
                }
            }
        }

        // Count blocks found by this wallet
        if ((isset($item['publicKey']) && $item['publicKey'] === $publicKey) || 
            (isset($item['founderKey']) && $item['founderKey'] === $publicKey) || 
            (isset($item['donateKey']) && $item['donateKey'] === $publicKey)) {
            
            $totalBlocksFound++;

            // Check for 199 blocks found after this block (if applicable)
            if ($blockIndex >= 199) {
                $currentTahcoin += $defaultReward; // Reward is usable, add to currentTahcoin
            } else {
                $pendingRewards += $defaultReward; // Reward is pending
            }
        }
    }

    // Calculate the total current Tahcoin balance
    $currentTahcoin = ($totalReceived + $currentTahcoin - $totalSent);

    // Prepare response data with last 9 transactions
    $responseData = [
        'wallet_address' => htmlspecialchars($walletAddress),
        'total_amount_sent' => number_format($totalSent, 9),
        'total_amount_received' => number_format($totalReceived, 9),
        'pending_received' => number_format($pendingReceived, 9),
        'total_blocks_found' => $totalBlocksFound,
        'current_tahcoin_balance' => number_format($currentTahcoin, 9),
        'pending_tahcoin_balance' => number_format($pendingRewards, 9),
        'last_transactions' => array_slice($allTransactions, -9), // Get last 9 transactions
    ];

    echo json_encode($responseData);
} else {
    echo json_encode(['error' => 'Invalid request method or missing public key.']);
}

// Close the database connection at the end of the script
$conn->close();

// Function definitions remain unchanged below this point...

function getTransactionsFromBlock($block) {
    if (isset($block['transactions'])) {
        return json_decode($block['transactions'], true);
    }
}

function getBlocksWithTransactions($conn) {
    $stmt = $conn->prepare("SELECT * FROM blocks ORDER BY id ASC");
    $stmt->execute();
    return $stmt->get_result();
}
?>
