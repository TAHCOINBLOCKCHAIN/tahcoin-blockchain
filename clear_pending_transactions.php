<?php
// Clear the pending transactions in the JSON file
$pendingTransactionsFile = 'pending_transactions_1.thr';

// Check if the pending transactions file exists
if (file_exists($pendingTransactionsFile)) {
    // Overwrite the file with an empty array to clear pending transactions
    file_put_contents($pendingTransactionsFile, json_encode([]));
}

// Optionally, you can return a success response if needed
echo json_encode(['success' => true, 'message' => 'Pending transactions cleared.']);
?>