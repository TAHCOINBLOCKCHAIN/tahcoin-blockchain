# API Documentation for Tahriver Blockchain Data

## Overview
The Tahriver API provides access to blockchain data, specifically the total number of blocks and related rewards. This document outlines how to use the API, including endpoint details, request methods, and example responses.

## Base URL
```
https://tahriver.online/get_blockchain_data.php
```

## Endpoint
### `GET /get_blockchain_data.php`

This endpoint retrieves blockchain data, including the total number of blocks (total rows) and calculated rewards for the founder and donations.

### Request
- **Method**: `GET`
- **URL**: `https://tahriver.online/get_blockchain_data.php`

### Response Format
The response is returned in JSON format. Below is an example of a successful response:

```json
{
    "block_height": 209881,
    "total_supply": "12,587.132767272",
    "current_supply": "12,587.132767272",
    "founder_reward": "4,195.706726713",
    "donation_reward": "4,195.706726713"
}
```

### Response Fields
- `block_height`: The total number of blocks in the blockchain (integer).
- `total_supply`: The total reward calculated for all blocks (string formatted to 9 decimal places).
- `current_supply`: The current supply of rewards (string formatted to 9 decimal places).
- `founder_reward`: The portion of the total reward allocated to the founder (string formatted to 9 decimal places).
- `donation_reward`: The portion of the total reward allocated for donations (string formatted to 9 decimal places).

### Error Handling
In case of an error, the API will return a JSON object containing an error message. For example:

```json
{
    "error": "Total rows not found in response."
}
```

## Example Usage

### cURL Command Line Example
You can also use cURL directly from the command line to fetch data:

```bash
curl https://tahriver.online/get_blockchain_data.php
```

### Example Response from cURL Command

When you run the above command, you might receive a response like this:

```json
{"block_height":209881,"total_supply":"12,587.132767272","current_supply":"12,587.132767272","founder_reward":"4,195.706726713","donation_reward":"4,195.706726713"}
```

## Conclusion
This API provides essential blockchain data that can be integrated into various applications. Ensure proper error handling in your implementation to manage any issues that may arise during requests.