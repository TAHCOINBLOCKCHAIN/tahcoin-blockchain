<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Wallet Statement</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Your existing styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        .result {
            margin-top: 20px;
            padding: 10px;
        }
        .error {
            color: red;
        }
        .statement-item {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
            padding: 10px;
        }
        .statement-item strong {
            margin-right: 5px;
        }
        .download-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Hide the form when printing */
        @media print {
            #statementForm {
                display: none;
            }
            .download-button {
                display: none;
            }
        }

        /* Responsive styles */
        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }
            .statement-item {
                padding: 5px;
            }
        }
        .block, .transaction {
            border: 1px solid #999;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .block h3, .transaction h4 {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Wallet Statement</h1>

        <!-- Wallet Statement Form -->
        <form id="statementForm" method="POST">
            <label for="publicKey">Public Key:</label>
            <input type="text" id="publicKey" name="publicKey" required>
            <div style="margin-top:19px">
                <button type="submit">Generate Statement</button>
            </div>
        </form>

<?php 
include 'config_2.php'; // Ensure this line is present at the top of your script
// Create a new database connection
$conn = getDbConnection();
// Initialize variables
$walletAddress = '';
$totalSent = 0;
$totalReceived = 0;
$totalBlocksFound = 0;
$currentTahcoin = 0;
$pendingRewards = 0;
$pendingReceived = 0; // Initialize pending received variable
$defaultReward = 0.019990904; // Default reward per block

function decrypt($input, $shiftAmount) {
    // Reverse the string first
    $reversed = strrev($input);
    $output = '';
    
    foreach (str_split($reversed) as $char) {
        // Shift character back by the defined amount
        $originalChar = chr(ord($char) - $shiftAmount);
        $output .= $originalChar;
    }
    
    return $output;
}

// Process the wallet import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['publicKey'])) {
    $walletAddress = $_POST['publicKey'];

    // Load balances data
    $balancesFile = 'balances.thr';

    if (!file_exists($balancesFile)) {
        echo '<div class="result error">Balances file not found.</div>';
        exit;
    }

    $balancesData = json_decode(file_get_contents($balancesFile), true);
    
    // Store the public key in memory
$publicKey = $walletAddress; // Store the original public key for calculations

//$blockTimestamps = []; // Array to store block timestamps

// Calculate total sent and received amounts
// Function to yield transactions from a block
function getTransactionsFromBlock($block) {
    if (isset($block['transactions'])) {
        $transactions = json_decode($block['transactions'], true);
        
        if (is_array($transactions)) {
            foreach ($transactions as $transaction) {
                yield $transaction; // Yield each transaction
            }
        }
    }
}

// Function to yield blocks and their transactions
// Function to yield blocks and their transactions
function getBlocksWithTransactions($conn) {
    $stmt = $conn->prepare("SELECT * FROM blocks ORDER BY id ASC");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($block = $result->fetch_assoc()) {
        // Yield the block itself
        yield $block;

        // Decode transactions from JSON string to array
        if (isset($block['transactions'])) {
            $transactions = json_decode($block['transactions'], true);
            if (is_array($transactions)) {
                foreach ($transactions as $transaction) {
                    yield $transaction; // Yield each transaction
                }
            } else {
                echo "Failed to decode transactions for block ID {$block['id']}: " . json_last_error_msg() . "\n";
            }
        } else {
            echo "No transactions found for block ID {$block['id']}.\n";
        }
    }
}

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

    // Display the wallet details
    echo '<div class="result">';
    echo '<strong>Wallet Address:</strong> ' . htmlspecialchars($walletAddress) . '<br>';
    echo '<strong>Total Amount Sent:</strong> ' . htmlspecialchars(number_format($totalSent, 9)) . ' tahcoins<br>';
    echo '<strong>Total Amount Received:</strong> ' . htmlspecialchars(number_format($totalReceived, 9)) . ' tahcoins<br>';
    echo '<strong>Pending Received:</strong> ' . htmlspecialchars(number_format($pendingReceived, 9)) . ' tahcoins<br>';
    echo '<strong>Total Blocks Found:</strong> ' . htmlspecialchars($totalBlocksFound) . '<br>';
    echo '<strong>Current Tahcoin Balance:</strong> ' . htmlspecialchars(number_format($currentTahcoin, 9)) . ' tahcoins<br>';
    echo '<strong>Pending Tahcoin Balance:</strong> ' . htmlspecialchars(number_format($pendingRewards, 9)) . ' tahcoins';
     echo '<div class="statement-item">';
  echo '<strong>Date of Statement Generation:</strong> ' . date('Y-m-d H:i:s');
  echo '</div>';

  echo '<button class="download-button" onclick="window.print()">Download Statement as PDF</button>';
  echo '</div>'; 
    echo '</div>';
// Display all transactions for the wallet address
echo '<h5>Transaction History</h5>';
echo '<div id="transactionHistory" style="display: flex; flex-wrap: wrap; gap: 20px;">'; // Container for transactions

echo '</div>'; // Close transaction history container
}
?>

<?php
// Create a new database connection
$conn = getDbConnection();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the public key from input (make sure to sanitize it)
$publicKey = htmlspecialchars($_POST['publicKey']); // Assuming you're using POST method

// Query to fetch all blocks from the database for the specified public key
$query = "SELECT blockHash, timestamp, transactions FROM blocks WHERE transactions LIKE '%$publicKey%'"; // Fetch blocks related to the public key

$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Track the count of transactions displayed
$transactionCount = 0;

// Load transactions data from all blocks and display them directly
if ($result && $result->num_rows > 0) {
    while ($block = $result->fetch_assoc()) {
        // Convert timestamp to integer (assuming it's already in seconds)
        $timestampInSeconds = intval($block['timestamp'] / 1000);

        // Decode transactions JSON field
        if (isset($block['transactions'])) {
            $decodedTransactions = json_decode($block['transactions'], true); // Decode JSON into an array
            
            if (is_array($decodedTransactions)) {
                foreach ($decodedTransactions as $transaction) {
                    // Check if the transaction involves the public key
                    if (isset($transaction['sender'], $transaction['recipient'], $transaction['amount']) &&
                        ($transaction['sender'] === $publicKey || $transaction['recipient'] === $publicKey)) {
                        
                        // Attach block hash and formatted timestamp to transaction details
                        $transaction['blockHash'] = htmlspecialchars($block['blockHash']);
                        $transaction['timestamp'] = date('Y-m-d H:i:s', $timestampInSeconds); // Format timestamp

                        // Display the transaction directly
                        echo '<div class="transaction">';
                        
                        // Use the transaction ID from the fetched data
                        $transactionId = htmlspecialchars($transaction['id']); // Define transactionId here

                        echo '<h4>' . $transactionId . '</h4>';
                        echo '<p><strong>Sender:</strong> ' . htmlspecialchars($transaction['sender']) . '</p>';
                        echo '<p><strong>Recipient:</strong> ' . htmlspecialchars($transaction['recipient']) . '</p>';
                        echo '<p><strong>Amount:</strong> ' . htmlspecialchars($transaction['amount']) . ' tahcoins</p>';
                        echo '<p><strong>Date:</strong> ' . htmlspecialchars($transaction['timestamp']) . '</p>'; // Use formatted timestamp directly
                        echo '<p><strong>Block Hash:</strong> ' . htmlspecialchars($transaction['blockHash']) . '">' . htmlspecialchars($transaction['blockHash']) . '</p>';
                        echo '</div>';

                        // Increment the count of displayed transactions
                        $transactionCount++;
                    }
                }
            }
        }
    }
}

// Check if any transactions were displayed
if ($transactionCount === 0) {
    echo '<div class="result"></div>';
}

// Close the database connection
$conn->close();
?>

<script>
// Function to filter transactions based on date range
function filterTransactions() {
    const startDate = new Date(document.getElementById('startDate').value);
    const endDate = new Date(document.getElementById('endDate').value);
    const rows = document.querySelectorAll('#transactionTable tr');

    rows.forEach((row, index) => {
        // Skip header row
        if (index === 0) return;

        const dateCell = row.cells[5]; // Assuming date is in the 6th column
        const transactionDate = new Date(dateCell.textContent);

        // Show or hide the row based on date range
        if (transactionDate >= startDate && transactionDate <= endDate) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Add event listeners to date inputs
document.getElementById('startDate').addEventListener('change', filterTransactions);
document.getElementById('endDate').addEventListener('change', filterTransactions);
</script>
<script src="app_1.js"></script>
<script src="app_2.js"></script>
<script src="app_3.js"></script>
</body>
</html>