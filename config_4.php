<?
// Database connection details
$host = 'localhost'; // Your database host
$db = 'replace_it'; // Your database name
$user = 'replace_it'; // Your database username
$pass = 'replace_it'; // Your database password

// Connect to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>