<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Address Explorer</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Your existing styles */
        .result {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .error {
            color: red;
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
    <h1>Address Explorer</h1>

    <!-- Wallet Import Form -->
    <form id="importForm" method="POST">
        <label for="publicKey">Public Key:</label>
        <input type="text" id="publicKey" name="publicKey" required>
        <button type="submit">Explore</button>
    </form>
    
</div>
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
    echo '</div>';
// Display all transactions for the wallet address
echo '<h5>Transaction History</h5>';
echo '<div id="transactionHistory" style="display: flex; flex-wrap: wrap; gap: 20px;">'; // Container for transactions
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

// Array to hold all transactions
$allTransactions = []; 

// Load transactions data from all blocks and store them in an array
if ($result && mysqli_num_rows($result) > 0) {
    while ($block = mysqli_fetch_assoc($result)) {
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

                        // Store each transaction in the array
                        $allTransactions[] = $transaction;
                    }
                }
            }
        }
    }
} else {
    echo '<div style="border: 1px solid #ccc; border-radius: 8px; padding: 15px; width: calc(100% - 20px);">No transactions found for this wallet address.</div>';
}

// Close the database connection
$conn->close();

// Pass all transactions to JavaScript for client-side pagination
echo '<script>var allTransactions = ' . json_encode($allTransactions) . ';</script>';
?>

<div id="transactionsContainer"></div>
<div id="paginationControls" style="margin-top: 20px;"></div>

<script>
const transactionsPerPage = 9; // Number of transactions per page
let currentPage = 1;

function renderTransactions() {
    const container = document.getElementById('transactionsContainer');
    container.innerHTML = ''; // Clear previous content

    const startIndex = (currentPage - 1) * transactionsPerPage;
    const endIndex = Math.min(startIndex + transactionsPerPage, allTransactions.length);

    for (let i = startIndex; i < endIndex; i++) {
        const transaction = allTransactions[i];
        
        const transactionDiv = document.createElement('div');
        transactionDiv.className = 'transaction';
        
        // Decode the timestamp here
        const decodedTimestamp = decodeURIComponent(transaction.timestamp);

        transactionDiv.innerHTML = `
            <h4><a href="transaction_details.php?transactionId=${encodeURIComponent(transaction.id)}">${transaction.id}</a></h4>
            <p><strong>Sender:</strong> ${encodeURIComponent(transaction.sender)}</p>
            <p><strong>Recipient:</strong> ${encodeURIComponent(transaction.recipient)}</p>
            <p><strong>Amount:</strong> ${encodeURIComponent(transaction.amount)} tahcoins</p>
            <p><strong>Date:</strong> ${decodedTimestamp}</p> <!-- Use decoded timestamp -->
            <p><strong>Block Hash:</strong> <a href="block_details.php?blockHash=${encodeURIComponent(transaction.blockHash)}">${encodeURIComponent(transaction.blockHash)}</a></p>
        `;
        
        container.appendChild(transactionDiv);
    }

    renderPaginationControls();
}

function renderPaginationControls() {
    const controls = document.getElementById('paginationControls');
    controls.innerHTML = ''; // Clear previous controls

    const totalPages = Math.ceil(allTransactions.length / transactionsPerPage);

    if (currentPage > 1) {
        const prevButton = document.createElement('button');
        prevButton.innerText = 'Previous';
        prevButton.onclick = () => changePage(currentPage - 1);
        controls.appendChild(prevButton);
    }

    const pageInfo = document.createElement('span');
    pageInfo.innerText = ``;
    controls.appendChild(pageInfo);

    if (currentPage < totalPages) {
        const nextButton = document.createElement('button');
        nextButton.innerText = 'Next';
        nextButton.onclick = () => changePage(currentPage + 1);
        controls.appendChild(nextButton);
    }
}

function changePage(page) {
    currentPage = page;
    renderTransactions();
}

// Initial render of transactions
renderTransactions();
</script>

<style>
/* Responsive styles */
#transactionHistory {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.transaction-card {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 15px;
    width: calc(33% - 20px); /* Default width for larger screens */
    box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
}

/* Media query for mobile devices */
@media (max-width: 600px) {
    .transaction-card {
        width: calc(100% - 20px) ! important; /* Full width on mobile */
    }
}
</style>
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