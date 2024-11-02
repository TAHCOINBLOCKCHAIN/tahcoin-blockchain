<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Transaction Details</h1>

    <?php
// Include database configuration
include 'config_1.php'; // Ensure this file has the function getDbConnection()

// Create a new database connection
$conn = getDbConnection();

// Get the transaction ID from the URL
$transactionId = $_GET['transactionId'] ?? null;

if ($transactionId) {
    // Query to fetch all blocks and their transactions
    $query = "SELECT blockHash, timestamp, transactions FROM blocks ORDER BY id DESC"; 
    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    // Load transactions data from blocks
    $transactionFound = false;

    if ($result && $result->num_rows > 0) {
        while ($block = $result->fetch_assoc()) {
            // Convert timestamp to integer (assuming it's already in seconds)
            $timestampInSeconds = intval($block['timestamp'] / 1000);

            // Decode transactions JSON field
            if (isset($block['transactions'])) {
                $decodedTransactions = json_decode($block['transactions'], true); // Decode JSON into an array
                
                if (is_array($decodedTransactions)) {
                    foreach ($decodedTransactions as $transaction) {
                        // Check if this transaction matches the requested ID
                        if (isset($transaction['id']) && $transaction['id'] === $transactionId) {
                            echo '<div class="transaction">';
                            echo '<h4>Transaction ID: ' . htmlspecialchars($transaction['id']) . '</h4>';
                            echo '<p><strong>Sender:</strong> ' . htmlspecialchars($transaction['sender']) . '</p>';
                            echo '<p><strong>Recipient:</strong> ' . htmlspecialchars($transaction['recipient']) . '</p>';
                            echo '<p><strong>Amount:</strong> ' . htmlspecialchars(floatval($transaction['amount'])) . ' tahcoins</p>';
                            echo '<p><strong>Date:</strong> ' . date('Y-m-d H:i:s', $timestampInSeconds) . '</p>';
                            echo '<p><strong>Block Hash:</strong> <a href="block_details.php?blockHash=' . htmlspecialchars($block['blockHash']) . '">' . htmlspecialchars($block['blockHash']) . '</a></p>';
                            echo '</div>';
                            $transactionFound = true; // Mark as found
                            break 2; // Break out of both loops once found
                        }
                    }
                }
            }
        }
    }

    if (!$transactionFound) {
        echo '<p>Error: Transaction not found.</p>'; // If no matching transaction was found
    }
} else {
    echo '<p>Error: No transaction ID provided.</p>';  // If no transaction ID is provided in URL
}

// Close the database connection
$conn->close();
?>
</div>
</body>
</html>