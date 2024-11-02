<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Transactions Overview</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Your existing styles */
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .error {
            color: red;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: white;
            background-color: #007bff; /* Bootstrap primary color */
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
            font-weight: bold;
        }
        .pagination a:hover {
            background-color: #0056b3; /* Darker shade for hover */
            transform: scale(1.05); /* Slightly enlarge on hover */
        }
        .pagination .active {
            background-color: #0056b3; /* Active page color */
            color: white;
            pointer-events: none; /* Disable clicking on active page */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
        }
        .pagination .disabled {
            background-color: #ccc; /* Disabled state color */
            color: #999; /* Disabled text color */
            pointer-events: none; /* Disable clicking */
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Transactions Overview</h1>

    <?php
// Include database configuration
include 'config_1.php'; // Ensure this file has the function getDbConnection()

// Create a new database connection
$conn = getDbConnection();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch blocks from the database
$query = "SELECT blockHash, timestamp, transactions FROM blocks ORDER BY id DESC"; 
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Load transactions data from blocks
$transactions = [];
if ($result && $result->num_rows > 0) {
    while ($block = $result->fetch_assoc()) {
        // Directly use blockHash and timestamp without decryption
        if (isset($block['blockHash']) && isset($block['timestamp'])) {
            // Decode transactions JSON field
            if (isset($block['transactions'])) {
                $decodedTransactions = json_decode($block['transactions'], true); // Decode JSON into an array
                
                if (is_array($decodedTransactions)) {
                    foreach ($decodedTransactions as $transaction) {
                        // Check if transaction has required fields before adding to the list
                        if (isset($transaction['id'], $transaction['sender'], $transaction['recipient'], $transaction['amount'], $transaction['timestamp'])) {
                            // Add transaction to the list without decryption
                            $transactionDetails = [
                                'id' => htmlspecialchars($transaction['id']),
                                'sender' => htmlspecialchars($transaction['sender']),
                                'recipient' => htmlspecialchars($transaction['recipient']),
                                'amount' => floatval($transaction['amount']),
                                'timestamp' => htmlspecialchars($transaction['timestamp'])
                            ];
                            // Add transaction to the transactions array
                            $transactions[] = $transactionDetails;
                        }
                    }
                }
            }
        }
    }
}

// Check if there are any transactions
if (empty($transactions)) {
    echo '<div class="error">No transactions found.</div>';
} else {
    // Calculate total number of pages for pagination
    $totalPages = ceil(count($transactions) / 19);

    // Determine current page
    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

    // Validate current page
    if ($currentPage < 1 || $currentPage > $totalPages) {
        $currentPage = 1;
    }

    // Slice transactions array based on current page
    $start = ($currentPage - 1) * 19;
    $paginatedTransactions = array_slice($transactions, $start, 19);

    // Display transactions in a table
    echo '<table>';
    echo '<tr>';
    echo '<th>Transaction ID</th>';
    echo '<th>Sender</th>';
    echo '<th>Recipient</th>';
    echo '<th>Amount (Tahcoins)</th>';
    echo '<th>Date</th>';
    echo '</tr>';

    foreach ($paginatedTransactions as $transaction) {
        echo '<tr>';
        echo '<td><a href="transaction_details.php?transactionId=' . htmlspecialchars($transaction['id']) . '">' . htmlspecialchars($transaction['id']) . '</a></td>';
        echo '<td>' . htmlspecialchars($transaction['sender']) . '</td>';
        echo '<td>' . htmlspecialchars($transaction['recipient']) . '</td>';
        echo '<td>' . htmlspecialchars($transaction['amount']) . '</td>';
        echo '<td>' . htmlspecialchars($transaction['timestamp']) . '</td>'; 
        echo '</tr>';
    }

    echo '</table>';

    // Display pagination links below the table
    echo '<div class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        echo '<a href="?page=' . ($currentPage - 1) . '">Previous</a>';
    } else {
        echo '<span class="disabled">Previous</span>';
    }

    // Next button
    if ($currentPage < $totalPages) {
        echo '<a href="?page=' . ($currentPage + 1) . '">Next</a>';
    } else {
        echo '<span class="disabled">Next</span>';
    }
    
    echo '</div>';
}

// Close the database connection
$conn->close();
?>
</div>
</body>
</html>