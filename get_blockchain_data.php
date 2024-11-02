<?php
// Set the API endpoint and public key
$apiUrl = 'https://tahriver.online/api.php?action=total_rows&publicKey=abc123';

// Initialize a cURL session
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects

// Execute the cURL request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo json_encode(['error' => 'cURL Error: ' . curl_error($ch)]);
    exit;
}

// Close the cURL session
curl_close($ch);

// Decode the JSON response from the API
$data = json_decode($response, true);

// Check if total_rows is present in the response
if (isset($data['total_rows'])) {
    // Convert total_rows to a number
    $totalRows = (int)$data['total_rows']; // Convert to integer

    // Calculate rewards
    $rewardPerBlock = 0.059972712; // Reward per block
    $totalReward = $totalRows * $rewardPerBlock; // Total reward for all blocks
    
    // Calculate founder and donation rewards (adjust percentages as needed)
    $founderReward = $totalReward * 0.333333; // 33.33% for founder
    $donationReward = $totalReward * 0.333333; // 33.33% for donations

    // Prepare the result array
    $result = [
        'block_height' => $totalRows,
        'total_supply' => number_format($totalReward, 9), // Format to 9 decimal places
        'current_supply' => number_format($totalReward, 9), // Format to 9 decimal places
        'founder_reward' => number_format($founderReward, 9), // Format to 9 decimal places
        'donation_reward' => number_format($donationReward, 9), // Format to 9 decimal places
    ];
} else {
    $result = ['error' => 'Total rows not found in response.'];
}

// Output the results as JSON
header('Content-Type: application/json');
echo json_encode($result);
?>