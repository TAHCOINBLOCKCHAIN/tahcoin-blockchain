<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blockchain Explorer</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            
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
        .search-container {
            margin-bottom: 20px;
        }
        .search-container input {
            padding: 10px;
            width: calc(100% - 22px);
        }
        .search-container button {
            padding: 10px;
        }
        .view-more {
            margin-top: 10px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
    <h1>Blockchain Explorer</h1>
    
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search by Block Hash">
        <button onclick="search()">Search</button>
    </div>

    <div id="blocksContainer">
        <h2>Latest Blocks</h2>
        <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
include 'config_1.php'; // Ensure this file has the function getDbConnection()

// Create a new database connection
$conn = getDbConnection();

// Query to fetch the last 9 blocks from the database
$result = $conn->query("SELECT * FROM blocks ORDER BY id DESC LIMIT 9");

if ($result && $result->num_rows > 0) {
    while ($block = $result->fetch_assoc()) {
        // Check if blockHash and timestamp exist before displaying
        if (isset($block['blockHash']) && isset($block['timestamp'])) {
            // Convert timestamp to integer (assuming it's already in seconds)
            $timestampInSeconds = intval($block['timestamp'] / 1000);

            echo '<div class="block">';
            echo '<h3><a href="block_details.php?blockHash=' . htmlspecialchars($block['blockHash']) . '">' . htmlspecialchars($block['blockHash']) . '</a></h3>';
            echo '<p><strong>Date:</strong> ' . date('Y-m-d H:i:s', $timestampInSeconds) . '</p>';
            echo '</div>';
        }
    }
    echo '<a href="blocks_overview.php" class="view-more">View More Blocks</a>';
} else {
    echo '<p>Error: No blocks found.</p>';
}

// Close the database connection
$conn->close();
?>

<div id="transactionsContainer">
    <h2>Latest Transactions</h2>
    <?php
// Create a new database connection
$conn = getDbConnection();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch all blocks from the database
$query = "SELECT blockHash, timestamp, transactions FROM blocks"; // Fetch all blocks

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
                    // Assuming each transaction has sender, recipient, amount fields
                    if (isset($transaction['sender'], $transaction['recipient'], $transaction['amount'])) {
                        // Attach block hash and formatted timestamp to transaction details
                        $transaction['blockHash'] = htmlspecialchars($block['blockHash']);
                        $transaction['timestamp'] = date('Y-m-d H:i:s', $timestampInSeconds); // Format timestamp

                        // Display the transaction directly
                        echo '<div class="transaction">';
                        
                        // Use the transaction ID from the fetched data
                        $transactionId = htmlspecialchars($transaction['id']); // Define transactionId here

                        echo '<h4><a href="transaction_details.php?transactionId=' . $transactionId . '">' . $transactionId . '</a></h4>';
                        echo '<p><strong>Sender:</strong> ' . htmlspecialchars($transaction['sender']) . '</p>';
                        echo '<p><strong>Recipient:</strong> ' . htmlspecialchars($transaction['recipient']) . '</p>';
                        echo '<p><strong>Amount:</strong> ' . htmlspecialchars($transaction['amount']) . ' tahcoins</p>';
                        echo '<p><strong>Date:</strong> ' . htmlspecialchars($transaction['timestamp']) . '</p>'; // Use formatted timestamp directly
                        echo '<p><strong>Block Hash:</strong> <a href="block_details.php?blockHash=' . htmlspecialchars($transaction['blockHash']) . '">' . htmlspecialchars($transaction['blockHash']) . '</a></p>';
                        echo '</div>';

                        // Increment the count of displayed transactions
                        $transactionCount++;

                        // Stop displaying after 9 transactions
                        if ($transactionCount >= 9) {
                            break 2; // Break out of both foreach and while loops
                        }
                    }
                }
            }
        }
    }
}

// Check if any transactions were displayed
if ($transactionCount === 0) {
    echo '<div class="result">No recent transactions found.</div>';
}

echo '<a href="transactions_overview.php" class="view-more">View More Transactions</a>';

// Close the database connection
$conn->close();
?>
    </div>
</div>
    </div>
<script>
        function search() {
            const searchValue = document.getElementById('searchInput').value.trim();
            if (searchValue) {
                window.location.href = 'block_details.php?blockHash=' + encodeURIComponent(searchValue);
            } else {
                alert('Please enter a block hash to search.');
            }
        }
</script>
</body>
</html>