// config.php
<?php
$host = 'localhost'; // Your database host
$db   = 'tahcoino_blockchain'; // Your database name
$user = 'tahcoino_blockchain'; // Your database username
$pass = 'ALHAMDULILLAH@a1'; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . htmlspecialchars($e->getMessage());
}
?>