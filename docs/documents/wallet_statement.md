# Wallet Statement Documentation

## Overview

The Wallet Statement application provides users with a detailed overview of their Tahcoin transactions within a specified date range. Users can generate statements that summarize their transaction history, including amounts sent and received, block rewards, and current balances.

## Features

- **Statement Generation**: Users can generate a wallet statement by entering their public key and specifying a date range.
- **Transaction Details**: The statement includes detailed information about each transaction, including the date, type (sent or received), amount, counterparty, block hash, and block number.
- **Downloadable Statement**: Users can print the statement for their records.

## How It Works

### 1. Statement Generation

When a user submits the statement request:

- **Input Validation**: The application checks for the presence of the public key, start date, and end date.
- **Loading Data**: It loads the blocks and balances data.

### 2. Statement Display

The application generates and displays the statement:

- **Statement Summary**: It shows the wallet address, statement period, total sent and received amounts, and the current balance.
- **Transaction Details**: It lists each transaction that occurred within the specified date range, including details such as date, type, amount, counterparty, block hash, and block number.

### 3. Download Option

- **Print Statement**: Users can click a button to print the statement, which can be saved as a PDF.

## User Interface

The wallet statement interface consists of:

- A form for entering the public key and date range.
- A section displaying the generated statement, including totals and transaction details.
- A button to download the statement as a PDF.

## Example Usage

1. **Generate Statement**: Enter your public key and select the start and end dates in the form, then click "Generate Statement."
2. **View Statement**: After generating, you will see a summary of your transactions, including total sent, received, and current balance.
3. **Transaction Details**: Review the detailed list of transactions that occurred during the specified period.
4. **Download Statement**: Click the "Download Statement as PDF" button to print or save the statement.

## Conclusion

The Wallet Statement application is a powerful tool for Tahcoin users to track their transaction history and manage their cryptocurrency effectively. With the ability to generate detailed statements and view transaction information, users can maintain a clear understanding of their financial activities within the Tahcoin network. 