<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Start the session to store pending transactions in memory  

function generateTransactionId() {
    $prefix = '999';
    $characters = '12345789taehr';
    $length = 99; // Total length of the transaction ID
    $transactionId = $prefix;

    for ($i = 0; $i < $length - strlen($prefix); $i++) {
        $transactionId .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $transactionId;
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Wallet</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Wallet</h1>

        <!-- Wallet Import Form -->
        <form id="importForm" method="POST">
            <label for="publicKey">Public Key:</label>
            <input type="text" id="publicKey" name="publicKey" required>
            <label for="privateKey">Private Key:</label>
            <input type="text" id="privateKey" name="privateKey" required>
            <button type="submit">Import Wallet</button>
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

function getBlocks($conn) {
    $stmt = $conn->prepare("SELECT * FROM blocks ORDER BY id ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($block = $result->fetch_assoc()) {
        yield $block; // Yield each block instead of returning an array
    }
}

// Usage
//foreach (getBlocks($conn) as $block) {
//    processBlock($block);
//}

// Function to yield blocks and their transactions
function getBlocksWithTransactions2($conn) {
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
            }
        }
    }
}

function encryptData($data, $key) {
    // Generate a random IV
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    // Encrypt the data
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    // Return the IV and encrypted data as a base64-encoded string
    return base64_encode($iv . $encryptedData);
}

function decryptData($data, $key) {
    // Decode the base64-encoded string
    $data = base64_decode($data);
    // Extract the IV and encrypted data
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($data, 0, $ivLength);
    $encryptedData = substr($data, $ivLength);
    // Decrypt the data
    return openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
}

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

function derivePrivateKey($publicKey, $inputPrivateKey) {
    // Use the input private key passed from the form
    $privateKey = $inputPrivateKey; 

    // Calculate SHA-512 hash of the derived private key
    $hash = hash('sha512', $privateKey);

    // Check if the derived hash matches the provided public key
    if ($hash === $publicKey) {
        return $privateKey; // Return the valid private key
    } else {
        return null; // Return null if hashes do not match
    }
}

// Process the wallet import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['publicKey']) && !empty($_POST['privateKey'])) {
    $walletAddress = $_POST['publicKey'];
    $inputPrivateKey = $_POST['privateKey'];

    // Derive the expected private key from the input public key
    $expectedPrivateKey = derivePrivateKey($walletAddress, $inputPrivateKey);

    // Check if the derived private key matches the input private key
    if ($expectedPrivateKey === $inputPrivateKey) {
        // Load balances data
        $balancesFile = 'balances.thr';

        if (!file_exists($balancesFile)) {
            echo '<div class="result error">Balances file not found.</div>';
            exit;
        }

        $balancesData = json_decode(file_get_contents($balancesFile), true);
        
        // Store the public key in memory
        $publicKey = $walletAddress; // Store the original public key for calculations

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

// Calculate the total current Tahcoin balance after processing all transactions
$currentTahcoin = ($currentTahcoin + $totalReceived - $totalSent); // Ensure totalSent is subtracted correctly

// Save the current balance in the balances file
$balancesData[$publicKey] = $currentTahcoin;

        // Encrypt balances data before saving
        $key = 'PVFShHy3IDo3nSQUrIRZRKsITMJmmlnZr5vd<qQtp7i4OJovpQqGGvoqhe7kS:DGF;OG:OY]tySSnJWKLuXD8fh7GY54PTFUupYP]HsSdEg8z}ffF;Lik]pYJU8OgFk\GvryWo6YEoT7REsKP]RzPo3SS37DYqORHr5GpLV6ziDqGz73fwMvd}GE35pVo|NeDLOHths55;LVN8PU|7;fzjQMDU<jgisSOD}JX4]XDETP7EU\:]|i3G:qhsuzI]porquoJWihJZRnP<Hvu]eDh5W<dUvr<SW8Ko5e:IUiV6rxM;Jf4Ihi6vJi5rFsyP8Hl4|vXKVtt4qhDTKSmnYWq<QuEsM3KxxLxSsuLQh8Efrn3:nsxWpD|Flw:EkGTdhF6stgwVFi88S3wnRfjj3YrJuqYTWNItd4Rmrz]eHF3tXJ}Yr6}kU4PDHwI\qDQl45|dY|hIe}UTxGvY\o\OrNVwG3HRNvGJPKoPq4\oN3veV}SKId}gLHuKNZ6]||yZTJtRfpyRdOGQs|:E4R36HIP:tROXRZr:huVXtg\nyZQgSFx|LnF}<UYp}\}heFs:S34hdnxWwyjOfGYI<Mg8ur<5d;syRS3R:IrRWkuervzqG8IOo8MN}8sknP}GixXQHZXZLT\lD\kh]D<QH];ziWQXQGjxFo]i4:3w|DYz87]kekXw8qZ]S<rzSvi3:6d|8syrVMFrnDn:sMTn6QRp}T}XsX;qweEmzrT\VesPvj;M4N}Nx6Z3RqSw3uu8m|rg4T:yVVs6:\UzuQQoYqiVEos<OWzwLOwOpli7hwld;D:zhle7nUu]lDPvPE\qdiI]U]OE4X8K3eOvUskrVgirNeDWXPrUSzKtlRH;Q<Ik<UWWwsQ7uEQMj<QqSIu3\i8HQv6hl<\T4kR4;7U:Gu<w<DnOe<6nz5}kf7ryWvrHyJnlGwm<AlhamdulillahvLGF}8@999@19990904@vELFM8JWtHjGhQYEJQZG6QuHh<YoFLUTzPl]pAlhamdulillahFUXp4eQ\VOyhvZ6'; // Use a secure key; ideally store it securely (e.g., in an environment variable)
        $encryptedBalancesData = encryptData(json_encode($balancesData), $key);
        file_put_contents($balancesFile, $encryptedBalancesData);

        // Display the wallet details
        echo '<div class="result">';
        echo '<strong>Wallet Address:</strong> ' . htmlspecialchars($walletAddress) . '<br>';
        echo '<strong>Total Amount Sent:</strong> ' . htmlspecialchars($totalSent) . ' tahcoins<br>';
        echo '<strong>Total Amount Received:</strong> ' . htmlspecialchars($totalReceived) . ' tahcoins<br>';
        echo '<strong>Pending Received:</strong> ' . htmlspecialchars($pendingReceived) . ' tahcoins<br>';
        echo '<strong>Total Blocks Found:</strong> ' . htmlspecialchars($totalBlocksFound) . '<br>';
        echo '<strong>Current Tahcoin Balance:</strong> ' . htmlspecialchars($currentTahcoin) . ' tahcoins<br>';
        echo '<strong>Pending Tahcoin Balance:</strong> ' . htmlspecialchars($pendingRewards) . ' tahcoins';
        echo '</div>';

       // Check for pending transactions in JSON file and hide/send form accordingly
// Check for pending transactions before processing
$hasPendingTransactions = false;

// Load existing pending transactions from file
if (file_exists('pending_transactions.thr')) {
    try {
        $existingTransactionsEncrypted = file_get_contents('pending_transactions.thr');
        
        if ($existingTransactionsEncrypted !== false) {
            // Decrypt and decode existing transactions
            $existingTransactions = json_decode(decryptData($existingTransactionsEncrypted, 'PVFShHy3IDo3nSQUrIRZRKsITMJmmlnZr5vd<qQtp7i4OJovpQqGGvoqhe7kS:DGF;OG:OY]tySSnJWKLuXD8fh7GY54PTFUupYP]HsSdEg8z}ffF;Lik]pYJU8OgFk\GvryWo6YEoT7REsKP]RzPo3SS37DYqORHr5GpLV6ziDqGz73fwMvd}GE35pVo|NeDLOHths55;LVN8PU|7;fzjQMDU<jgisSOD}JX4]XDETP7EU\:]|i3G:qhsuzI]porquoJWihJZRnP<Hvu]eDh5W<dUvr<SW8Ko5e:IUiV6rxM;Jf4Ihi6vJi5rFsyP8Hl4|vXKVtt4qhDTKSmnYWq<QuEsM3KxxLxSsuLQh8Efrn3:nsxWpD|Flw:EkGTdhF6stgwVFi88S3wnRfjj3YrJuqYTWNItd4Rmrz]eHF3tXJ}Yr6}kU4PDHwI\qDQl45|dY|hIe}UTxGvY\o\OrNVwG3HRNvGJPKoPq4\oN3veV}SKId}gLHuKNZ6]||yZTJtRfpyRdOGQs|:E4R36HIP:tROXRZr:huVXtg\nyZQgSFx|LnF}<UYp}\}heFs:S34hdnxWwyjOfGYI<Mg8ur<5d;syRS3R:IrRWkuervzqG8IOo8MN}8sknP}GixXQHZXZLT\lD\kh]D<QH];ziWQXQGjxFo]i4:3w|DYz87]kekXw8qZ]S<rzSvi3:6d|8syrVMFrnDn:sMTn6QRp}T}XsX;qweEmzrT\VesPvj;M4N}Nx6Z3RqSw3uu8m|rg4T:yVVs6:\UzuQQoYqiVEos<OWzwLOwOpli7hwld;D:zhle7nUu]lDPvPE\qdiI]U]OE4X8K3eOvUskrVgirNeDWXPrUSzKtlRH;Q<Ik<UWWwsQ7uEQMj<QqSIu3\i8HQv6hl<\T4kR4;7U:Gu<w<DnOe<6nz5}kf7ryWvrHyJnlGwm<AlhamdulillahvLGF}8@999@19990904@vELFM8JWtHjGhQYEJQZG6QuHh<YoFLUTzPl]pAlhamdulillahFUXp4eQ\VOyhvZ6'), true);
            
            // Check if existingTransactions is an array
            if (is_array($existingTransactions)) {
                foreach ($existingTransactions as $transaction) {
                    // Ensure publicKey is defined
                    $publicKey = isset($_POST['publicKey']) ? $_POST['publicKey'] : null; 

                    // Check if the sender of any pending transaction matches the public key
                    if ($transaction['sender'] === $publicKey) {
                        $hasPendingTransactions = true; // Set flag to true if a matching transaction is found
                        break; // Exit loop early since we found a pending transaction
                    }
                }
            }
        }
    } catch (Exception $e) {
        echo '<div class="result error">Error loading pending transactions: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// If there are pending transactions, hide the send form
if ($hasPendingTransactions) {
    echo '<div class="result error">You have pending transactions. Please wait until they are completed before sending more Tahcoins.</div>';
    // Hide send form since there are pending transactions
    echo '<style>#sendForm { display: none; }</style>';
} else {
    // Send Tahcoins form
    echo '<h2>Send Tahcoins</h2>';
    echo '<form method="POST" id="sendForm">';
    echo '<input type="hidden" name="sender" value="' . htmlspecialchars($publicKey) . '">';
    echo '<label for="recipient">Recipient Address:</label>';
    echo '<input type="text" id="recipient" name="recipient" required>';
    echo '<label for="amount">Amount to Send:</label>';
    echo '<input type="number" id="amount" name="amount" step="0.000000001" min="0.000000001" max="' . htmlspecialchars($currentTahcoin) . '" required>';
    echo '<button type="submit">Send Tahcoins</button>';
    echo '</form>';
}
    } else {
        echo '<div class="result error">Invalid keys. Please try again.</div>';
    }
}

// Function to save pending transactions to a JSON file
function savePendingTransactions($transactions, $key) {
    // Encrypt the transactions data
    $encryptedData = encryptData(json_encode($transactions), $key);
    
    // Save encrypted data to the file
    file_put_contents('pending_transactions.thr', $encryptedData);
}

// Process the send Tahcoins form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use a separate condition to check if it's the send form
    if (isset($_POST['sender']) && isset($_POST['recipient']) && isset($_POST['amount'])) {
        $sender = $_POST['sender'];
        $recipient = $_POST['recipient'];
        $amount = floatval($_POST['amount']);

        // Load and decrypt balances data
        $balancesFile = 'balances.thr';
        $key = 'PVFShHy3IDo3nSQUrIRZRKsITMJmmlnZr5vd<qQtp7i4OJovpQqGGvoqhe7kS:DGF;OG:OY]tySSnJWKLuXD8fh7GY54PTFUupYP]HsSdEg8z}ffF;Lik]pYJU8OgFk\GvryWo6YEoT7REsKP]RzPo3SS37DYqORHr5GpLV6ziDqGz73fwMvd}GE35pVo|NeDLOHths55;LVN8PU|7;fzjQMDU<jgisSOD}JX4]XDETP7EU\:]|i3G:qhsuzI]porquoJWihJZRnP<Hvu]eDh5W<dUvr<SW8Ko5e:IUiV6rxM;Jf4Ihi6vJi5rFsyP8Hl4|vXKVtt4qhDTKSmnYWq<QuEsM3KxxLxSsuLQh8Efrn3:nsxWpD|Flw:EkGTdhF6stgwVFi88S3wnRfjj3YrJuqYTWNItd4Rmrz]eHF3tXJ}Yr6}kU4PDHwI\qDQl45|dY|hIe}UTxGvY\o\OrNVwG3HRNvGJPKoPq4\oN3veV}SKId}gLHuKNZ6]||yZTJtRfpyRdOGQs|:E4R36HIP:tROXRZr:huVXtg\nyZQgSFx|LnF}<UYp}\}heFs:S34hdnxWwyjOfGYI<Mg8ur<5d;syRS3R:IrRWkuervzqG8IOo8MN}8sknP}GixXQHZXZLT\lD\kh]D<QH];ziWQXQGjxFo]i4:3w|DYz87]kekXw8qZ]S<rzSvi3:6d|8syrVMFrnDn:sMTn6QRp}T}XsX;qweEmzrT\VesPvj;M4N}Nx6Z3RqSw3uu8m|rg4T:yVVs6:\UzuQQoYqiVEos<OWzwLOwOpli7hwld;D:zhle7nUu]lDPvPE\qdiI]U]OE4X8K3eOvUskrVgirNeDWXPrUSzKtlRH;Q<Ik<UWWwsQ7uEQMj<QqSIu3\i8HQv6hl<\T4kR4;7U:Gu<w<DnOe<6nz5}kf7ryWvrHyJnlGwm<AlhamdulillahvLGF}8@999@19990904@vELFM8JWtHjGhQYEJQZG6QuHh<YoFLUTzPl]pAlhamdulillahFUXp4eQ\VOyhvZ6'; // Use a secure key
        if (file_exists($balancesFile)) {
            $encryptedBalancesData = file_get_contents($balancesFile);
            // Decrypt balances data
            $balancesData = json_decode(decryptData($encryptedBalancesData, $key), true);
        } else {
            echo '<div class="result error">Balances file not found.</div>';
            exit;
        }

        // Check for pending transactions before processing
$hasPendingTransactions = false;

// Load existing pending transactions from file
if (file_exists('pending_transactions.thr')) {
    try {
        $existingTransactionsEncrypted = file_get_contents('pending_transactions.thr');
        
        if ($existingTransactionsEncrypted !== false) {
            // Decrypt and decode existing transactions
            $existingTransactions = json_decode(decryptData($existingTransactionsEncrypted, 'PVFShHy3IDo3nSQUrIRZRKsITMJmmlnZr5vd<qQtp7i4OJovpQqGGvoqhe7kS:DGF;OG:OY]tySSnJWKLuXD8fh7GY54PTFUupYP]HsSdEg8z}ffF;Lik]pYJU8OgFk\GvryWo6YEoT7REsKP]RzPo3SS37DYqORHr5GpLV6ziDqGz73fwMvd}GE35pVo|NeDLOHths55;LVN8PU|7;fzjQMDU<jgisSOD}JX4]XDETP7EU\:]|i3G:qhsuzI]porquoJWihJZRnP<Hvu]eDh5W<dUvr<SW8Ko5e:IUiV6rxM;Jf4Ihi6vJi5rFsyP8Hl4|vXKVtt4qhDTKSmnYWq<QuEsM3KxxLxSsuLQh8Efrn3:nsxWpD|Flw:EkGTdhF6stgwVFi88S3wnRfjj3YrJuqYTWNItd4Rmrz]eHF3tXJ}Yr6}kU4PDHwI\qDQl45|dY|hIe}UTxGvY\o\OrNVwG3HRNvGJPKoPq4\oN3veV}SKId}gLHuKNZ6]||yZTJtRfpyRdOGQs|:E4R36HIP:tROXRZr:huVXtg\nyZQgSFx|LnF}<UYp}\}heFs:S34hdnxWwyjOfGYI<Mg8ur<5d;syRS3R:IrRWkuervzqG8IOo8MN}8sknP}GixXQHZXZLT\lD\kh]D<QH];ziWQXQGjxFo]i4:3w|DYz87]kekXw8qZ]S<rzSvi3:6d|8syrVMFrnDn:sMTn6QRp}T}XsX;qweEmzrT\VesPvj;M4N}Nx6Z3RqSw3uu8m|rg4T:yVVs6:\UzuQQoYqiVEos<OWzwLOwOpli7hwld;D:zhle7nUu]lDPvPE\qdiI]U]OE4X8K3eOvUskrVgirNeDWXPrUSzKtlRH;Q<Ik<UWWwsQ7uEQMj<QqSIu3\i8HQv6hl<\T4kR4;7U:Gu<w<DnOe<6nz5}kf7ryWvrHyJnlGwm<AlhamdulillahvLGF}8@999@19990904@vELFM8JWtHjGhQYEJQZG6QuHh<YoFLUTzPl]pAlhamdulillahFUXp4eQ\VOyhvZ6'), true);
            
            // Check if existingTransactions is an array
            if (is_array($existingTransactions)) {
                foreach ($existingTransactions as $transaction) {
                    // Ensure publicKey is defined
                    $publicKey = isset($_POST['publicKey']) ? $_POST['publicKey'] : null; 

                    // Check if the sender of any pending transaction matches the public key
                    if ($transaction['sender'] === $publicKey) {
                        $hasPendingTransactions = true; // Set flag to true if a matching transaction is found
                        break; // Exit loop early since we found a pending transaction
                    }
                }
            }
        }
    } catch (Exception $e) {
        echo '<div class="result error">Error loading pending transactions: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// If there are pending transactions, prevent sending and notify the user
if ($hasPendingTransactions) {
    echo '<div class="result error">You have pending transactions. Please wait until they are completed before sending more Tahcoins.</div>';
    return; // Exit to prevent further processing
}

        // Assuming you already have blocks data loaded in $blocksData
foreach (getBlocks($conn) as $block) {
    if (isset($block['transactions'])) {
        foreach ((is_array($block['transactions']) ? $block['transactions'] : []) as $transaction) {
            // Assuming transaction details are stored in plain text
            $sender = $transaction['sender']; // No decryption
            $recipient = $transaction['recipient']; // No decryption
            $amount = floatval($transaction['amount']); // No decryption

            $publicKey = isset($_POST['publicKey']) ? $_POST['publicKey'] : null; 

            // Check if the sender matches
            if ($sender === $publicKey) { 
                $totalSent += $amount;
            }
            // Check if the recipient matches the public key
            if ($recipient === $publicKey) { 
                $totalReceived += $amount;
            }
        }
    }

    // Check for blocks found by this wallet
    if ((isset($block['publicKey']) && $block['publicKey'] === $sender) || 
    (isset($block['founderKey']) && $block['founderKey'] === $sender) || 
    (isset($block['donateKey']) && $block['donateKey'] === $sender)) {
    $totalBlocksFound++;
    }
}

// Calculate current Tahcoin balance
global $defaultReward; 
$currentTahcoin = ($totalBlocksFound * $defaultReward) + $totalReceived - $totalSent; // Ensure this is set correctly

// Get the current balance from the decrypted balances data
$currentTahcoinBalance = isset($balancesData[$sender]) ? $balancesData[$sender] : 0;

// Ensure that the transaction amount does not exceed current balance
if ($amount > 0 && $amount <= $currentTahcoinBalance) { // Use only currentTahcoinBalance for validation

            // Create new transaction with a unique ID and current timestamp
            $newTransaction = [
                "id" => generateTransactionId(), // Use the new function to generate a unique ID
                "sender" => $sender,
"recipient" => $recipient,
"amount" => strval(min($amount, $currentTahcoinBalance)), // Use the minimum of requested and available balance
"timestamp" => date('Y-m-d H:i:s')
            ];

            // Update the balance in the balances data array
            $balancesData[$sender] -= min($amount, $currentTahcoinBalance); // Deduct only what is available
            $balancesData[$recipient] = (isset($balancesData[$recipient]) ? $balancesData[$recipient] : 0) + min($amount, $currentTahcoinBalance);

            // Encrypt and save updated balances data back to file
            $encryptedBalancesData = encryptData(json_encode($balancesData), $key);
            file_put_contents($balancesFile, $encryptedBalancesData);
            
            // Load existing pending transactions from file
if (file_exists('pending_transactions.thr')) {
    // Decrypt existing transactions from JSON file
    try {
        $existingTransactionsEncrypted = file_get_contents('pending_transactions.thr');
        
        if ($existingTransactionsEncrypted !== false) {
            // Decrypt and decode existing transactions
            $existingTransactions = json_decode(decryptData($existingTransactionsEncrypted, 'PVFShHy3IDo3nSQUrIRZRKsITMJmmlnZr5vd<qQtp7i4OJovpQqGGvoqhe7kS:DGF;OG:OY]tySSnJWKLuXD8fh7GY54PTFUupYP]HsSdEg8z}ffF;Lik]pYJU8OgFk\GvryWo6YEoT7REsKP]RzPo3SS37DYqORHr5GpLV6ziDqGz73fwMvd}GE35pVo|NeDLOHths55;LVN8PU|7;fzjQMDU<jgisSOD}JX4]XDETP7EU\:]|i3G:qhsuzI]porquoJWihJZRnP<Hvu]eDh5W<dUvr<SW8Ko5e:IUiV6rxM;Jf4Ihi6vJi5rFsyP8Hl4|vXKVtt4qhDTKSmnYWq<QuEsM3KxxLxSsuLQh8Efrn3:nsxWpD|Flw:EkGTdhF6stgwVFi88S3wnRfjj3YrJuqYTWNItd4Rmrz]eHF3tXJ}Yr6}kU4PDHwI\qDQl45|dY|hIe}UTxGvY\o\OrNVwG3HRNvGJPKoPq4\oN3veV}SKId}gLHuKNZ6]||yZTJtRfpyRdOGQs|:E4R36HIP:tROXRZr:huVXtg\nyZQgSFx|LnF}<UYp}\}heFs:S34hdnxWwyjOfGYI<Mg8ur<5d;syRS3R:IrRWkuervzqG8IOo8MN}8sknP}GixXQHZXZLT\lD\kh]D<QH];ziWQXQGjxFo]i4:3w|DYz87]kekXw8qZ]S<rzSvi3:6d|8syrVMFrnDn:sMTn6QRp}T}XsX;qweEmzrT\VesPvj;M4N}Nx6Z3RqSw3uu8m|rg4T:yVVs6:\UzuQQoYqiVEos<OWzwLOwOpli7hwld;D:zhle7nUu]lDPvPE\qdiI]U]OE4X8K3eOvUskrVgirNeDWXPrUSzKtlRH;Q<Ik<UWWwsQ7uEQMj<QqSIu3\i8HQv6hl<\T4kR4;7U:Gu<w<DnOe<6nz5}kf7ryWvrHyJnlGwm<AlhamdulillahvLGF}8@999@19990904@vELFM8JWtHjGhQYEJQZG6QuHh<YoFLUTzPl]pAlhamdulillahFUXp4eQ\VOyhvZ6'), true);
            
            // Ensure existingTransactions is an array
            if (!is_array($existingTransactions)) {
                $existingTransactions = []; // Initialize as an empty array if decoding failed or no transactions exist
            }
        } else {
            $existingTransactions = []; // Initialize as an empty array if file read fails
        }
    } catch (Exception $e) {
        echo '<div class="result error">Error loading pending transactions: ' . htmlspecialchars($e->getMessage()) . '</div>';
        $existingTransactions = []; // Initialize as an empty array in case of an error
    }
} else {
    $existingTransactions = []; // Initialize as an empty array if the file does not exist
}

// Now you can safely push a new transaction into the existing transactions array
array_push($existingTransactions, $newTransaction);

// Save updated pending transactions back to JSON file
savePendingTransactions($existingTransactions, 'PVFShHy3IDo3nSQUrIRZRKsITMJmmlnZr5vd<qQtp7i4OJovpQqGGvoqhe7kS:DGF;OG:OY]tySSnJWKLuXD8fh7GY54PTFUupYP]HsSdEg8z}ffF;Lik]pYJU8OgFk\GvryWo6YEoT7REsKP]RzPo3SS37DYqORHr5GpLV6ziDqGz73fwMvd}GE35pVo|NeDLOHths55;LVN8PU|7;fzjQMDU<jgisSOD}JX4]XDETP7EU\:]|i3G:qhsuzI]porquoJWihJZRnP<Hvu]eDh5W<dUvr<SW8Ko5e:IUiV6rxM;Jf4Ihi6vJi5rFsyP8Hl4|vXKVtt4qhDTKSmnYWq<QuEsM3KxxLxSsuLQh8Efrn3:nsxWpD|Flw:EkGTdhF6stgwVFi88S3wnRfjj3YrJuqYTWNItd4Rmrz]eHF3tXJ}Yr6}kU4PDHwI\qDQl45|dY|hIe}UTxGvY\o\OrNVwG3HRNvGJPKoPq4\oN3veV}SKId}gLHuKNZ6]||yZTJtRfpyRdOGQs|:E4R36HIP:tROXRZr:huVXtg\nyZQgSFx|LnF}<UYp}\}heFs:S34hdnxWwyjOfGYI<Mg8ur<5d;syRS3R:IrRWkuervzqG8IOo8MN}8sknP}GixXQHZXZLT\lD\kh]D<QH];ziWQXQGjxFo]i4:3w|DYz87]kekXw8qZ]S<rzSvi3:6d|8syrVMFrnDn:sMTn6QRp}T}XsX;qweEmzrT\VesPvj;M4N}Nx6Z3RqSw3uu8m|rg4T:yVVs6:\UzuQQoYqiVEos<OWzwLOwOpli7hwld;D:zhle7nUu]lDPvPE\qdiI]U]OE4X8K3eOvUskrVgirNeDWXPrUSzKtlRH;Q<Ik<UWWwsQ7uEQMj<QqSIu3\i8HQv6hl<\T4kR4;7U:Gu<w<DnOe<6nz5}kf7ryWvrHyJnlGwm<AlhamdulillahvLGF}8@999@19990904@vELFM8JWtHjGhQYEJQZG6QuHh<YoFLUTzPl]pAlhamdulillahFUXp4eQ\VOyhvZ6');

            // Display success message
            echo '<div class="result success">Transaction added. ' . htmlspecialchars(min($amount, $currentTahcoinBalance)) . ' tahcoins will be sent to ' . htmlspecialchars($recipient) . '.</div>';
        } else {
            echo '<div class="result error">Invalid amount or insufficient balance.</div>';
        }
    }
}

// Display pending transactions from JSON file
if (file_exists('pending_transactions.thr')) {
    // Load and decrypt existing pending transactions from JSON file
    $existingTransactionsEncrypted = file_get_contents('pending_transactions.thr');
    
    if ($existingTransactionsEncrypted !== false) {
        try {
            // Decrypt and decode existing transactions
            $existingTransactions = json_decode(decryptData($existingTransactionsEncrypted, 'PVFShHy3IDo3nSQUrIRZRKsITMJmmlnZr5vd<qQtp7i4OJovpQqGGvoqhe7kS:DGF;OG:OY]tySSnJWKLuXD8fh7GY54PTFUupYP]HsSdEg8z}ffF;Lik]pYJU8OgFk\GvryWo6YEoT7REsKP]RzPo3SS37DYqORHr5GpLV6ziDqGz73fwMvd}GE35pVo|NeDLOHths55;LVN8PU|7;fzjQMDU<jgisSOD}JX4]XDETP7EU\:]|i3G:qhsuzI]porquoJWihJZRnP<Hvu]eDh5W<dUvr<SW8Ko5e:IUiV6rxM;Jf4Ihi6vJi5rFsyP8Hl4|vXKVtt4qhDTKSmnYWq<QuEsM3KxxLxSsuLQh8Efrn3:nsxWpD|Flw:EkGTdhF6stgwVFi88S3wnRfjj3YrJuqYTWNItd4Rmrz]eHF3tXJ}Yr6}kU4PDHwI\qDQl45|dY|hIe}UTxGvY\o\OrNVwG3HRNvGJPKoPq4\oN3veV}SKId}gLHuKNZ6]||yZTJtRfpyRdOGQs|:E4R36HIP:tROXRZr:huVXtg\nyZQgSFx|LnF}<UYp}\}heFs:S34hdnxWwyjOfGYI<Mg8ur<5d;syRS3R:IrRWkuervzqG8IOo8MN}8sknP}GixXQHZXZLT\lD\kh]D<QH];ziWQXQGjxFo]i4:3w|DYz87]kekXw8qZ]S<rzSvi3:6d|8syrVMFrnDn:sMTn6QRp}T}XsX;qweEmzrT\VesPvj;M4N}Nx6Z3RqSw3uu8m|rg4T:yVVs6:\UzuQQoYqiVEos<OWzwLOwOpli7hwld;D:zhle7nUu]lDPvPE\qdiI]U]OE4X8K3eOvUskrVgirNeDWXPrUSzKtlRH;Q<Ik<UWWwsQ7uEQMj<QqSIu3\i8HQv6hl<\T4kR4;7U:Gu<w<DnOe<6nz5}kf7ryWvrHyJnlGwm<AlhamdulillahvLGF}8@999@19990904@vELFM8JWtHjGhQYEJQZG6QuHh<YoFLUTzPl]pAlhamdulillahFUXp4eQ\VOyhvZ6'), true);
            
            // Check if existingTransactions is an array and has elements
            if (is_array($existingTransactions) && !empty($existingTransactions)) {
                echo '<h3>Pending Transactions</h3>';
                echo '<ul>';
                
                foreach ($existingTransactions as $transaction) {
                    echo '<li>Transaction ID: ' . htmlspecialchars($transaction['id']) . ', Amount: ' . htmlspecialchars($transaction['amount']) . ' tahcoins to ' . htmlspecialchars($transaction['recipient']) . '</li>';
                }
                
                echo '</ul>'; // Close the unordered list
            } else {
                // No pending transactions found
                echo '<div class="result">No pending transactions found.</div>';
            }
        } catch (Exception $e) {
            echo '<div class="result error">Error loading pending transactions: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        echo '<div class="result">No pending transactions found.</div>';
    }
}
?>
    </div>
<script src="app_1.js"></script>
<script src="app_2.js"></script>
<script src="app_3.js"></script>
</body>
</html>