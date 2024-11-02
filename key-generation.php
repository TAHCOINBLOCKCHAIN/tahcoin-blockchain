<?php
// Fetch the BIP-39 word list from the provided URL
$wordListUrl = 'https://raw.githubusercontent.com/bitcoin/bips/master/bip-0039/english.txt';
$wordList = file($wordListUrl, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Function to generate random words
function generateRandomWords($wordList, $numWords = 19) {
    $words = [];
    for ($i = 0; $i < $numWords; $i++) {
        $words[] = $wordList[array_rand($wordList)];
    }
    return implode(' ', $words);
}

// Generate random words and their hash
$randomWords = generateRandomWords($wordList, 19);
$hashedWords = hash('sha512', $randomWords);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Key Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .result {
            margin: 20px 0;
            padding: 15px;
            background-color: #e7f3fe;
            border-left: 6px solid #2196F3;
        }
        .instructions {
            margin-top: 20px;
            padding: 10px;
            background-color: #fff3cd;
            border-left: 6px solid #ffeeba;
        }
        button {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Key Generator</h1>

    <div class="result">
        <strong>Your Seeds:</strong> <span id="generatedWords"><?php echo htmlspecialchars($randomWords); ?></span><br>
        <strong>Your Address:</strong> <span id="hashedWords"><?php echo htmlspecialchars($hashedWords); ?></span><br>
        <button onclick="copyToClipboard('generatedWords')">Copy Seeds</button>
    </div>

    <div class="instructions">
        <h2>Instructions:</h2>
        <p>Please keep your generated seeds/words secure and do not share them publicly. 
           If someone gains access to your seeds, they can control your account.</p>
        <p>Ensure that there are no extra spaces between words when copying, as this may cause an invalid key error.</p>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    const textToCopy = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(textToCopy).then(() => {
        alert('Seeds copied to clipboard!');
    }).catch(err => {
        console.error('Could not copy text:', err);
    });
}
</script>

</body>
</html>