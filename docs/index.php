<?php
// index.php

$directory = 'documents';
$files = glob($directory . '/*.md');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        h1 {
            margin-bottom: 20px;
        }
        .document-list {
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>
<body>

<div class="container document-list">
    <h1 class="text-center">List of Documents</h1>
    <ul class="list-group">
        <?php foreach ($files as $file): ?>
            <li class="list-group-item">
                <a href="view.php?file=<?php echo urlencode(basename($file)); ?>">
                    <?php echo basename($file); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>