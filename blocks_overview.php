<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blocks Overview</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            
        }
        .block, .transaction {
            border: 1px solid #999;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .block h3, .transaction h4 {
            margin: 0;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .search-container input {
            padding: 10px;
            width: calc(100% - 22px);
        }
        .search-container button {
            padding: 10px;
        }
        .view-more {
            margin-top: 10px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: white;
            background-color: #007bff; /* Bootstrap primary color */
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
            font-weight: bold;
        }
        .pagination a:hover {
            background-color: #0056b3; /* Darker shade for hover */
            transform: scale(1.05); /* Slightly enlarge on hover */
        }
        .pagination .active {
            background-color: #0056b3; /* Active page color */
            color: white;
            pointer-events: none; /* Disable clicking on active page */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
        }
        .pagination .disabled {
            background-color: #ccc; /* Disabled state color */
            color: #999; /* Disabled text color */
            pointer-events: none; /* Disable clicking */
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Blocks Overview</h1>
    
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search by Block Hash">
        <button onclick="search()">Search</button>
    </div>

    <div id="blocksContainer">
        <h2>Latest Blocks</h2>
        <?php
// Include database configuration
include 'config_1.php'; // Ensure this file has the function getDbConnection()

// Create a new database connection
$conn = getDbConnection();
$blocksPerPage = 9; // Number of blocks to display per page

// Get total number of blocks for pagination
$totalQuery = "SELECT COUNT(*) as total FROM blocks";
$totalResult = $conn->query($totalQuery);
$totalBlocks = ($totalResult && $totalResult->num_rows > 0) ? $totalResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalBlocks / $blocksPerPage); // Calculate total pages

// Get current page from query parameter, default to 1
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) {
    $currentPage = 1;
} elseif ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

// Calculate the starting index for the current page
$startIndex = ($currentPage - 1) * $blocksPerPage;

// Query to fetch blocks data with limit for pagination
$query = "SELECT * FROM blocks ORDER BY id DESC LIMIT $startIndex, $blocksPerPage";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($block = $result->fetch_assoc()) {
        // Assuming blockHash and timestamp are stored in plain text
        if (isset($block['blockHash']) && isset($block['timestamp'])) {
            // Convert timestamp to integer (assuming it's already in seconds)
                $timestampInSeconds = intval($block['timestamp'] / 1000);

            echo '<div class="block">';
            echo '<h3><a href="block_details.php?blockHash=' . htmlspecialchars($block['blockHash']) . '">' . htmlspecialchars($block['blockHash']) . '</a></h3>';
            echo '<p><strong>Date:</strong> ' . date('Y-m-d H:i:s', $timestampInSeconds) . '</p>';
            echo '</div>';
        }
    }

    // Pagination links
    echo '<div class="pagination">';
    // Previous button
    if ($currentPage > 1) {
        echo '<a href="?page=' . ($currentPage - 1) . '">Previous</a>';
    } else {
        echo '<span class="disabled">Previous</span>';
    }

    // Next button
    if ($currentPage < $totalPages) {
        echo '<a href="?page=' . ($currentPage + 1) . '">Next</a>';
    } else {
        echo '<span class="disabled">Next</span>';
    }
    echo '</div>';
} else {
    echo '<p>Error: No blocks found.</p>';
}

// Close the database connection
$conn->close();
?>
    </div>
</div>

<script src="app_4.js"></script>
</body>
</html>