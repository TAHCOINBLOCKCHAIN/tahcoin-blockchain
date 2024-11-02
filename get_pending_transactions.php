<?php
header('Content-Type: application/json'); // Set the content type to JSON

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

// Initialize an empty array for pending transactions
$pendingTransactions = [];

// Check if the pending transactions file exists
if (file_exists('pending_transactions.thr')) {
    // Load and decrypt existing pending transactions from JSON file
    $existingTransactionsEncrypted = @file_get_contents('pending_transactions.thr'); // Suppress errors with @
    
    if ($existingTransactionsEncrypted !== false) {
        // Decrypt and decode existing transactions
        $pendingTransactions = json_decode(decryptData($existingTransactionsEncrypted, 'PVFShHy3IDo3nSQUrIRZRKsITMJmmlnZr5vd<qQtp7i4OJovpQqGGvoqhe7kS:DGF;OG:OY]tySSnJWKLuXD8fh7GY54PTFUupYP]HsSdEg8z}ffF;Lik]pYJU8OgFk\GvryWo6YEoT7REsKP]RzPo3SS37DYqORHr5GpLV6ziDqGz73fwMvd}GE35pVo|NeDLOHths55;LVN8PU|7;fzjQMDU<jgisSOD}JX4]XDETP7EU\:]|i3G:qhsuzI]porquoJWihJZRnP<Hvu]eDh5W<dUvr<SW8Ko5e:IUiV6rxM;Jf4Ihi6vJi5rFsyP8Hl4|vXKVtt4qhDTKSmnYWq<QuEsM3KxxLxSsuLQh8Efrn3:nsxWpD|Flw:EkGTdhF6stgwVFi88S3wnRfjj3YrJuqYTWNItd4Rmrz]eHF3tXJ}Yr6}kU4PDHwI\qDQl45|dY|hIe}UTxGvY\o\OrNVwG3HRNvGJPKoPq4\oN3veV}SKId}gLHuKNZ6]||yZTJtRfpyRdOGQs|:E4R36HIP:tROXRZr:huVXtg\nyZQgSFx|LnF}<UYp}\}heFs:S34hdnxWwyjOfGYI<Mg8ur<5d;syRS3R:IrRWkuervzqG8IOo8MN}8sknP}GixXQHZXZLT\lD\kh]D<QH];ziWQXQGjxFo]i4:3w|DYz87]kekXw8qZ]S<rzSvi3:6d|8syrVMFrnDn:sMTn6QRp}T}XsX;qweEmzrT\VesPvj;M4N}Nx6Z3RqSw3uu8m|rg4T:yVVs6:\UzuQQoYqiVEos<OWzwLOwOpli7hwld;D:zhle7nUu]lDPvPE\qdiI]U]OE4X8K3eOvUskrVgirNeDWXPrUSzKtlRH;Q<Ik<UWWwsQ7uEQMj<QqSIu3\i8HQv6hl<\T4kR4;7U:Gu<w<DnOe<6nz5}kf7ryWvrHyJnlGwm<AlhamdulillahvLGF}8@999@19990904@vELFM8JWtHjGhQYEJQZG6QuHh<YoFLUTzPl]pAlhamdulillahFUXp4eQ\VOyhvZ6'), true);
        
        // Ensure $pendingTransactions is an array
        if (!is_array($pendingTransactions)) {
            $pendingTransactions = []; // Initialize as an empty array if decoding failed
        }
    }
}

// Return pending transactions as JSON
echo json_encode($pendingTransactions);
?>