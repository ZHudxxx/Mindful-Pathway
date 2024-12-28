<?php
// Start session
session_start();

// Database connection
$dbc = new mysqli("localhost", "root", "", "mindfulpathway");
if ($dbc->connect_errno) {
    echo "Failed to Open Database: " . $dbc->connect_error;
    exit();
}

// Check if articleID is passed in the URL
if (!isset($_GET['articleID']) || !is_numeric($_GET['articleID'])) {
    echo "Invalid Article ID.";
    exit();
}

$articleID = intval($_GET['articleID']);

// Fetch the article from the database
$stmt = $dbc->prepare("SELECT a.title, a.content, a.timePosted, u.username 
                       FROM article a 
                       JOIN user u ON a.authorID = u.userID 
                       WHERE a.articleID = ?");
$stmt->bind_param("i", $articleID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Article not found.";
    exit();
}

$article = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> | Mindful Pathway</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        .header {
            background-color: #3cacae;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .content {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .content h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #3cacae;
        }

        .content p {
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .content .metadata {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #3cacae;
            color: white;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo htmlspecialchars($article['title']); ?></h1>
    </div>

    <div class="content">
        <div class="metadata">
            <span>By: <?php echo htmlspecialchars($article['username']); ?></span> |
            <span>Published on: <?php echo date("d-m-Y", strtotime($article['timePosted'])); ?></span>
        </div>

        <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
    </div>

    <div class="footer">
        &copy; 2024 Mindful Pathway | All Rights Reserved
    </div>
</body>
</html>
