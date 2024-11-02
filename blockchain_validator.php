<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blockchain Validator</title>
</head>
<body>

<h1>Blockchain Validator</h1>
<button id="validateButton">Validate Blockchain</button>
<p id="resultMessage"></p>

<script>
async function validateBlockchain() {
    const resultMessageElement = document.getElementById("resultMessage");
    
    const response = await fetch('get_validate_blockchain.php');
    const result = await response.json();
    
    if (result.success) {
        resultMessageElement.textContent = result.message;
    } else {
        resultMessageElement.textContent = result.message + 
            (result.alteredBlock ? ` Altered Block Data: ${JSON.stringify(result.alteredBlock)}` : '');
    }
}

// Attach event listener to button
document.getElementById("validateButton").addEventListener("click", validateBlockchain);
</script>

</body>
</html>