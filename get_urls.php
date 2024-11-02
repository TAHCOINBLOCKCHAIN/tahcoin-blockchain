<?php

include 'config_3.php';

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the SQL statement to fetch URLs
    $stmt = $pdo->prepare("SELECT url, valid FROM urls");
    $stmt->execute();

    // Fetch all results as an associative array
    $urls = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results as JSON
    header('Content-Type: application/json');
    echo json_encode($urls);
} catch (PDOException $e) {
    // Handle any errors
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>