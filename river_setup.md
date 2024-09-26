# Tahcoin River Setup Guide

**Important Announcement**: The Tahcoin river for public launch is currently not available as it is still in development. We will announce when users can launch their own Tahcoin rivers. Thank you for your patience and interest in contributing to the Tahcoin community!

This guide will help you set up a Tahcoin river using different options, including local setups and cloud services like Google Cloud, Microsoft Azure, DigitalOcean, and VPS providers. Follow these steps to get your Tahcoin river running smoothly!

## What is a Tahcoin River?

A **Tahcoin river** is a computer that helps keep the Tahcoin network running. It stores information about transactions and helps verify them. By setting up your own river, you can contribute to the Tahcoin community and participate in the blockchain.

## Setting Up Your Tahcoin River

### Step 1: Create MySQL Database and User

Before setting up your Tahcoin river, you need to create a MySQL database and a user with restricted permissions to ensure blockchain integrity.

1. **Access MySQL**:
   - Open your terminal or command prompt.
   - Log in to MySQL as the root user:
     ```bash
     mysql -u root -p
     ```
   - Enter your MySQL root password when prompted.

2. **Create Database**:
   - Run the following command to create a new database for your Tahcoin river:
     ```sql
     CREATE DATABASE tahcoin_db;
     ```

3. **Create User**:
   - Create a new MySQL user (replace `your_username` and `your_password` with your desired username and password):
     ```sql
     CREATE USER 'your_username'@'localhost' IDENTIFIED BY 'your_password';
     ```

4. **Grant Permissions**:
   - Grant the necessary permissions to the user while restricting certain rights for blockchain integrity:
     ```sql
     GRANT SELECT, INSERT ON tahcoin_db.* TO 'your_username'@'localhost';
     ```
   - This grants the user permission to select and insert data but removes permissions for altering, deleting, updating, dropping tables, executing routines, etc.

5. **Flush Privileges**:
   - To apply the changes made to the user permissions, run:
     ```sql
     FLUSH PRIVILEGES;
     ```

6. **Exit MySQL**:
   - Type `exit;` to leave the MySQL prompt.

### Step 2: Choose Your Deployment Method

You can set up your Tahcoin river either on your own computer (local setup) or on a cloud service. Here are the options available:

#### Local Setup

1. **Install PHP**:
   - If you are using **Linux**, install the LAMP stack:
     ```bash
     sudo apt update
     sudo apt install apache2 mysql-server php libapache2-mod-php
     ```
   - If you prefer **XAMPP** (works on Windows, macOS, and Linux):
     - Download and install XAMPP from [Apache Friends](https://www.apachefriends.org/index.html).
     - Start the XAMPP control panel and make sure Apache and MySQL are running.

2. **Clone the Repository**:
   - Open your terminal and run:
   ```bash
   git clone https://github.com/tahcoinblockchain/tahcoin-blockchain.git
   cd tahcoin-blockchain
   ```

3. **Move Files to Root Directory**:
   - For optimal access, move the contents of the `tahcoin-blockchain` directory to the root directory of your web server (e.g., `/var/www/html` for Apache on Linux). This way, you can access your application directly via your IP address or domain name without needing to specify the `/tahcoin-blockchain` directory.
   ```bash
   sudo mv * /var/www/html/
   ```

4. **Configure Database Settings**:
   After cloning the repository, you'll need to configure your MySQL settings:

   - **Locate Configuration Files**: 
     Find configuration files named `config_*.php` and `cfblock.php` in your cloned repository directory.

   - **Edit Configuration Files**: 
     Open each configuration file in a text editor and fill in your MySQL database details:

     #### Example Configuration for `config_db.php`
     ```php
     <?php 
     $db_host = 'localhost'; // or your database host IP if remote
     $db_user = 'your_username'; // MySQL username created above 
     $db_pass = 'your_password'; // MySQL password created above 
     $db_name = 'tahcoin_db'; // Database name 
     
     // Create connection 
     $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
     
     // Check connection 
     if ($conn->connect_error) { 
         die("Connection failed: " . $conn->connect_error); 
     } 
     ?>
     ```

   - **Save Changes**: 
     After filling in all necessary details for each configuration file, save your changes.

5. **Create Database Tables**:
   Once you've configured your MySQL settings, you'll need to create the necessary database tables:

   - **Visit `setup_db.php`**: 
     Open your web browser and navigate to `http://localhost/setup_db.php` (or replace `localhost` with your server's IP address if applicable).

   - **Run Setup Script**: 
     This script will create all required tables and populate them with initial data for your Tahcoin river.

6. **Run the Initialization Script**:
   After setting up your database:

   - Using Command Line Interface (CLI):
     ```bash
     php init.php
     ```
   - Or using a Web Browser:
     - Go to `http://localhost/init.php`.

7. **Access the Application**:
   - Visit `http://localhost/index.php` in your web browser.

### Cloud Deployment Options

You can also deploy your Tahcoin river on various cloud platforms. Here’s how to do it:

1. **Google Cloud**:
   - **Instructions**:
     1. Sign in to the [Google Cloud Console](https://console.cloud.google.com/).
     2. Create a new project.
     3. Open Google Cloud Shell and run these commands:
        ```bash
        git clone https://github.com/tahcoinblockchain/tahcoin-blockchain.git
        cd tahcoin-blockchain
        ```
     
     4. Move Files to Root Directory:
        - Similar to local setup, move files from `tahcoin-blockchain` to the root directory of your web server.

     5. Configure Database Settings: Follow similar steps as in the local setup to configure your MySQL settings in the configuration files.

     6. Create Database Tables:
        - Visit `setup_db.php` by navigating to `http://your-cloud-instance-ip/setup_db.php`.

        - Run the setup script to create tables.

     7. Set up your environment:
        ```bash
        gcloud auth login
        gcloud config set project your-project-id
        ```
     
     8. Deploy your application:
        ```bash
        gcloud app deploy --quiet app.yaml --promote
        ```

     9. After deployment, go to `init.php` to initialize the blocks.

2. **Microsoft Azure**:
   - **Instructions**:
     1. Sign in to the [Azure Portal](https://portal.azure.com/).
     2. Create a new Web App.
     3. Choose a runtime stack that supports PHP.
     
     4. Move Files to Root Directory: Follow similar steps as above for moving files.

     5. Configure Database Settings: Follow similar steps as above for MySQL configuration.

     6. Create Database Tables:
        - Visit `setup_db.php` by navigating to `http://your-azure-instance-ip/setup_db.php`.

        - Run the setup script to create tables.

      7. Use the Azure CLI to clone your repository:
         ```bash
         az webapp deployment source config --name your-app-name --resource-group your-resource-group --repo-url https://github.com/tahcoinblockchain/tahcoin-blockchain --branch main --manual-integration
         ```

      8. After deployment, navigate to `init.php` to initialize the blocks.

3. **DigitalOcean**:
   - **Instructions**:
      1. Sign in to your [DigitalOcean account](https://www.digitalocean.com/).
      2. Create a new Droplet (choose Ubuntu as the OS).
      
      3. Once the Droplet is created, connect to it via SSH:
         ```bash
         ssh root@your_droplet_ip
         ```

      4. Update the package list and install necessary software:
         ```bash
         sudo apt update
         sudo apt install apache2 mysql-server php libapache2-mod-php git
         ```

      5. Clone the repository:
         ```bash
         git clone https://github.com/tahcoinblockchain/tahcoin-blockchain.git
         cd tahcoin-blockchain
         ```

      6. Move Files to Root Directory: Move files from `tahcoin-blockchain` to `/var/www/html`.

      7. Configure Database Settings: Follow similar steps as above for MySQL configuration.

      8. Create Database Tables:
         - Visit `setup_db.php` by navigating to `http://your_droplet_ip/setup_db.php`.

         - Run the setup script to create tables.

      9. Run the initialization script by visiting `http://your_droplet_ip/init.php`.

    10. Access the application by visiting `http://your_droplet_ip/index.php`.

4. **VPS Providers (General Instructions)**:
   - **Instructions for any VPS provider** (like Liriver, Vultr, etc.):

      1. Sign in to your VPS provider account.
      2. Create a new virtual server (choose Ubuntu as the OS).
      3. Connect to your server via SSH:
         ```bash
         ssh root@your_vps_ip
         ```

      4. Update the package list and install necessary software:
         ```bash
         sudo apt update
         sudo apt install apache2 mysql-server php libapache2-mod-php git
         ```

      5. Clone the repository:
         ```bash
         git clone https://github.com/tahcoinblockchain/tahcoin-blockchain.git
         cd tahcoin-blockchain
         ```

      6. Move Files to Root Directory: Move files from `tahcoin-blockchain` into `/var/www/html`.

      7. Configure Database Settings: Follow similar steps as above for MySQL configuration.

      8. Create Database Tables:
         - Visit `setup_db.php` by navigating to `http://your_vps_ip/setup_db.php`.

         - Run the setup script to create tables.

      9. Run the initialization script by visiting `http://your_vps_ip/init.php`.

    10. Access the application by visiting `http://your_vps_ip/index.php`.

### Step 3: Important Security Note

For any providers where you set up a MySQL user:

- Ensure that you remove these rights from the MySQL user account created earlier for maintaining blockchain integrity:

```sql
REVOKE ALTER ON tahcoin_db.* FROM 'your_username'@'localhost';
REVOKE DELETE ON tahcoin_db.* FROM 'your_username'@'localhost';
REVOKE UPDATE ON tahcoin_db.* FROM 'your_username'@'localhost';
REVOKE ALTER ROUTINE ON tahcoin_db.* FROM 'your_username'@'localhost';
REVOKE DROP ON tahcoin_db.* FROM 'your_username'@'localhost';
REVOKE EXECUTE ON tahcoin_db.* FROM 'your_username'@'localhost';
```

This ensures that critical operations that could compromise blockchain integrity are restricted from being executed by this user account.

### Conclusion

Setting up a Tahcoin river is an exciting way to participate in the cryptocurrency community! Whether you choose a local setup or a cloud service, follow these steps carefully, and you’ll have your river running smoothly in no time! Enjoy being part of the Tahcoin network! 🚀