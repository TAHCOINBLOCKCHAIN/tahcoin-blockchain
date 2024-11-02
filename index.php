<?php session_start(); // Start the session to store pending transactions in memory
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Tahcoin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        #progressBar {
            width: 100%;
            background-color: #f3f3f3;
            border: 1px solid #ccc;
            height: 20px;
            margin-top: 10px;
        }
        #progress {
            height: 100%;
            background-color: #4caf50;
            width: 0%;
        }
        #message {
            margin-top: 20px;
            color: red;
        }
        #results {
            margin-top: 20px;
        }
        .node {
            margin-bottom: 10px;
        }
        .bootnode {
            font-weight: bold;
            color: green;
        }
        .broken {
            color: red;
        }
        .valid {
            color: blue;
        }
    </style>
</head>
<body>
    <h1>Tahcoin</h1>

    <label for="publicKey">Enter Public Key: </label>
    <input type="text" min="99" max="99" id="publicKey" required>
    <label for="difficulty">Set Difficulty Level: </label>
    <input type="number" id="difficulty" min="1" max="999999999" value="1">
    <label for="threads">Select Number of Threads: </label>
    <input type="number" id="threads" min="1" max="999999999" value="1">
    
    <p>Current Block Hash: <span id="currentBlockHash"></span></p>
    <p>Integrity Hash: <span id="integrityHash"></span></p>
    <p>Total Hashes: <span id="totalHashes">0</span></p>
    <p>Reward Tahcoins: <span id="rewardTahcoins">0</span></p>
    <p>Current Difficulty: <span id="currentDifficulty">1</span></p>
    <p>Mining Time: <span id="miningTime">0</span> ms</p>
    <p>Hash Rate: <span id="hashRate">0</span></p>
    <p>Total Blocks Found: <span id="totalBlocksFound">0</span></p>
    <p>Chance of Finding Next Block: <span id="chanceOfFinding">0</span>%</p>
    <button id="startButton">Start Finding Blocks</button>
    <button id="stopButton" disabled>Stop Finding Blocks</button>
    <div id="progressBar"><div id="progress"></div></div>
    <div id="message"></div>
    <div id="peerupdatemessage"></div>   
    <div id="nodecheckmessage"></div>
    <!--div id="nodecheckresults"></div-->
    <div id="getpeersmessage"></div>
    <div id="peersgetresults"></div>
    <div id="node"></div>  
    <!--div id="errormessage1" style="display: none;"></div-->
    <div id="sentBlocks"></div
    <!--div id="errormessage1"></div-->
    <div id="peerscheckresults"></div>
    <div id="messagesend1"></div>
    <div id="receivedBlocks"></div>

    <div id="walletSection">
        <h2>Wallet Management</h2>
        <button id="generateKeysBtn">Generate Keys</button>
        <br>
        <button id="walletBtn">Use Wallet</button>
        <button id="statementsBtn">Wallet Statements</button>
        <button id="exploreaddressBtn">Explore Address</button>
    </div>
    
        <ul style="list-style-type: none; padding: 0; display: flex; justify-content: space-around; margin-bottom: 20px;">
        <li><a href="transactions_overview.php" style="text-decoration: none; color: #4a90e2;">Transactions</a></li>
        <li><a href="explorer.php" style="text-decoration: none; color: #4a90e2;">Explorer</a></li>
        <li><a href="blocks_overview.php" style="text-decoration: none; color: #4a90e2;">Block Explorer</a></li>
    </ul>
    <ul style="list-style-type: none; padding: 0; display: flex; justify-content: space-around; margin-bottom: 20px;">
    <li><a href="blockchain_validator.php" style="text-decoration: none; color: #4a90e2;">Blockchain Nodes Validator</a></li>
    </ul>
<script src="app_7.js"></script>
</body>
</html>