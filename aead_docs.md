# Address Explorer API Documentation

## Overview
The Address Explorer API allows users to retrieve information about a specific wallet address, including its transaction history and balance details. The API responds with JSON data that includes the total amounts sent and received, pending transactions, and the last 9 transactions associated with the wallet.

## Base URL
```
https://tahriver.online/get_wallet_overview.php
```

## Request Method
- **POST**

## Request Parameters
- **publicKey** (string, required): The public key of the wallet you want to explore.

### Example Request
To explore a wallet address with the public key `address`, send a POST request as follows:

```bash
curl -X POST "https://tahriver.online/get_wallet_overview.php" -d "publicKey=address"
```

## Response Format
The API will return a JSON object containing the following fields:

- **wallet_address**: The public key of the wallet.
- **total_amount_sent**: Total amount of tahcoins sent from this wallet.
- **total_amount_received**: Total amount of tahcoins received by this wallet.
- **pending_received**: Amount of tahcoins that are pending receipt.
- **total_blocks_found**: Total number of blocks found by this wallet.
- **current_tahcoin_balance**: Current balance of tahcoins in this wallet.
- **pending_tahcoin_balance**: Amount of tahcoins that are pending.
- **last_transactions**: An array of the last 9 transactions associated with this wallet. Each transaction includes:
  - **sender**: The sender's public key.
  - **recipient**: The recipient's public key.
  - **amount**: The amount of tahcoins transferred.
  - **blockHash**: The hash of the block containing this transaction.
  - **timestamp**: The date and time when the transaction occurred.

### Example Response
```json
{
    "wallet_address": "address",
    "total_amount_sent": "10.000000000",
    "total_amount_received": "15.000000000",
    "pending_received": "2.500000000",
    "total_blocks_found": 5,
    "current_tahcoin_balance": "7.500000000",
    "pending_tahcoin_balance": "0.500000000",
    "last_transactions": [
        {
            "sender": "sender_public_key_1",
            "recipient": "address",
            "amount": "1.000000000",
            "blockHash": "block_hash_1",
            "timestamp": "2024-10-05 14:54:49"
        },
        {
            "sender": "sender_public_key_2",
            "recipient": "address",
            "amount": "2.500000000",
            "blockHash": "block_hash_2",
            "timestamp": "2024-10-06 09:30:15"
        }
        // ... up to 9 transactions
    ]
}
```

## Error Handling
In case of an error, the API will return a JSON object with an `error` field.

### Example Error Response
```json
{
    "error": "Invalid request method or missing public key."
}
```

## Notes
- Ensure that your server is running and accessible at the specified base URL.
- Replace `localhost` with your server's IP address or domain name if accessing remotely.

