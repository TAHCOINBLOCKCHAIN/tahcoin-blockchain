<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Block Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .block-detail {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            word-wrap: break-word; /* Break long words */
        }
    </style>
</head>
<body>
<h1>Block Details</h1>
<div id="blockDetail" class="block-detail">
<?php
// Include database configuration
include 'config_1.php'; // Ensure this file has the function getDbConnection()

// Create a new database connection
$conn = getDbConnection();

// Get the block hash from the URL parameters
$blockHash = $_GET['blockHash'] ?? '';

// Query to fetch block details based on block hash
$query = "SELECT * FROM blocks WHERE blockHash = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $blockHash); // Bind parameters to prevent SQL injection
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    // Fetch the block details
    $block = $result->fetch_assoc();

    // Prepare block details for display
    $blockDetails = [
        'blockHash' => $block['blockHash'],
        'integrityHash' => $block['integrityHash'],
        'publicKey' => $block['publicKey'],
        'founderKey' => $block['founderKey'],
        'donateKey' => $block['donateKey'],
        'previousBlockHash' => $block['previousBlockHash'],
        'timestamp' => $block['timestamp']
    ];

    // Convert timestamp to seconds if necessary (assuming it's in milliseconds)
    $timestampInSeconds = (int)($blockDetails['timestamp'] / 1000);

    echo '<h2>Block Details</h2>';
    echo '<p><strong>Block Hash:</strong> ' . htmlspecialchars($blockDetails['blockHash']) . '</p>';
    echo '<p><strong>Integrity Hash:</strong> ' . htmlspecialchars($blockDetails['integrityHash']) . '</p>';
    echo '<p><strong>Found By:</strong> ' . htmlspecialchars($blockDetails['publicKey']) . '</p>';
    /*echo '<p><strong>Founder Key:</strong> ' . htmlspecialchars($blockDetails['founderKey']) . '</p>'; // Display founder key
    echo '<p><strong>Donate Key:</strong> ' . htmlspecialchars($blockDetails['donateKey']) . '</p>'; // Display donate key*/
    echo '<p><strong>Date:</strong> ' . date('Y-m-d H:i:s', $timestampInSeconds) . '</p>';
} else {
    echo '<p>Block not found.</p>';
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
</div>
</body>
</html>