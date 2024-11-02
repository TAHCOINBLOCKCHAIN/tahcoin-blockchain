<?php
$servername = "localhost"; // Your server name
$username = "replace_it"; // Your database username
$password = "replace_it"; // Your database password
$dbname = "replace_it"; // Your database name

// Create connection
function getDbConnection() {
    global $servername, $username, $password, $dbname;
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]));
    }
    
    return $conn;
}
?>