# API Command Usage Guide for Tahcoin

Welcome to the API Command Usage Guide for Tahcoin! This guide will help you understand how to use a simple command in the terminal to send Tahcoin (TAH) to someone else. Additionally, we’ll cover how to install PHP if it’s not already on your system. Let’s break it down step by step!

## What is Tahcoin?

**Tahcoin** is a type of digital money that you can use to send and receive funds over the internet. It operates on a secure network called a **blockchain**, which keeps track of all transactions without charging extra fees.

## What Do You Need?

Before you can send Tahcoin, you need the following:

1. **Your Public Key**: This is like your address where people can send you money.
2. **Your Private Key**: This is your secret password that allows you to access your funds—keep this safe!
3. **Receiver's Public Key**: The address of the person you want to send money to.
4. **Amount**: How much Tahcoin you want to send.

## Installing PHP

If PHP is not installed on your computer, follow these steps to install it:

### For Windows:

1. **Download PHP**:
   - Go to the [PHP Downloads page](https://windows.php.net/download/).
   - Choose the latest version and download the **Non-Thread Safe** version (usually in a ZIP file).

2. **Extract PHP**:
   - Extract the downloaded ZIP file to a folder, e.g., `C:\php`.

3. **Add PHP to System Path**:
   - Right-click on "This PC" or "My Computer" and select "Properties."
   - Click on "Advanced system settings."
   - Click on "Environment Variables."
   - Under "System variables," find and select the `Path` variable, then click "Edit."
   - Add the path to your PHP folder (e.g., `C:\php`) and click "OK."

4. **Verify Installation**:
   - Open Command Prompt and type `php -v`. You should see the PHP version information.

### For macOS:

1. **Using Homebrew**:
   - If you don’t have Homebrew installed, open Terminal and run:
     ```bash
     /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
     ```
   - Install PHP by running:
     ```bash
     brew install php
     ```

2. **Verify Installation**:
   - After installation, type `php -v` in Terminal. You should see the PHP version information.

### For Linux:

1. **Using APT (Debian/Ubuntu)**:
   - Open Terminal and run:
     ```bash
     sudo apt update
     sudo apt install php
     ```

2. **Using YUM (CentOS/RHEL)**:
   - Open Terminal and run:
     ```bash
     sudo yum install php
     ```

3. **Verify Installation**:
   - Type `php -v` in Terminal. You should see the PHP version information.

## The Command

To send Tahcoin using the API, you will use this command:

```
curl -X POST 'https://tahriver.online/api_313.php' \
-H 'Content-Type: application/x-www-form-urlencoded' \
-d 'public_key=your_public_key&private_key=your_private_key&receiver_address=your_receiver_address&amount=0.019990904'
```

### Breaking Down the Command

- **`curl -X api_313.php`**: This part tells your computer to run the program called `api_313.php`, which handles the transaction.
- **`'your_public_key'`**: Replace this with your actual public key (your wallet address).
- **`'your_private_key'`**: Replace this with your actual private key (your secure access key).
- **`'receiver_public_key'`**: Replace this with the public key of the person you want to send money to (their wallet address).
- **`'amount'`**: Specify how much TAH you want to send.

## Example

Let’s say:

- Your public key is `TAH313`
- Your private key is `HER313`
- Your friend's public key is `TAH1999`
- You want to send 9 TAH.

Your command would look like this:

```
curl -X POST 'https://tahriver.online/api_313.php' \
-H 'Content-Type: application/x-www-form-urlencoded' \
-d 'public_key=TAH313&private_key=HER313&receiver_address=TAH1999&amount=9'
```

## Running the Command

1. Open your terminal or command prompt on your computer.
2. Type in the command with your specific information.
3. Press **Enter**.

If everything goes well, you will see a message confirming that your Tahcoin has been sent!

## Important Tips

- **Double-check your keys and amount** before sending any transactions.
- **Keep your private key secure!** Never share it with anyone; it’s essential for protecting your funds.
- If you encounter any issues, consider asking for help from someone knowledgeable about cryptocurrencies.

## Conclusion

You now know how to install PHP and use the API command to send Tahcoin! With its no-fee transactions and secure network, Tahcoin provides an efficient way to manage digital assets. Enjoy sending and receiving Tahcoin! 🎉