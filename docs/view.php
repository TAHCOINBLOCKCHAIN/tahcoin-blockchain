<?php
// view.php

require 'vendor/autoload.php';

if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $filepath = 'documents/' . $file;

    if (file_exists($filepath) && pathinfo($filepath, PATHINFO_EXTENSION) === 'md') {
        $markdownContent = file_get_contents($filepath);
        $parsedown = new Parsedown();
        $htmlContent = $parsedown->text($markdownContent);
    } else {
        die("Error: Document not found or invalid file type.");
    }
} else {
    die("Error: No document specified.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($file); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .content {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container content">
    <h1><?php echo htmlspecialchars($file); ?></h1>
    <div><?php echo $htmlContent; ?></div>
    <a href="index.php" class="btn btn-primary mt-3">Back to Document List</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>