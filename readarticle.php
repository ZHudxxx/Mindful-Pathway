<?php
require 'dbconnect.php';
$qArticles = query("SELECT * FROM article");



// Get the project ID from the URL parameter
$xId = $_GET['id'];

// Query to fetch project details
$list = "SELECT * FROM `article` WHERE `articleID`='$xId'";
$result = mysqli_query($dbc, $list);
$row = mysqli_fetch_assoc($result);
if ($row) {
    $listArticle =
        "SELECT article.*, user.username 
        FROM article 
        JOIN user ON article.authorID = user.userID
        WHERE article.`articleID`='$xId'";
    $result_list = mysqli_query($dbc, $listArticle);
    $rowList = mysqli_fetch_assoc($result_list);
} else {
    echo "Project not found";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindful Pathway</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        /* Header */
        .header {
            background-color: #3cacae;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .header a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }

        .header .menu {
            display: flex;
            align-items: center;
        }

        .header a:hover {
            text-decoration: underline;
        }

        /* Main Banner */
        .main-banner {
            text-align: center;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 20px auto;
            max-width: 900px;
        }

        .main-banner h2 {
            color: #3cacae;
            margin-bottom: 10px;
        }

        .main-banner img {
            width: 100%;
            max-width: 600px;
            margin-top: 10px;
            border-radius: 10px;
        }

        .main-banner button {
            background-color: #5ce1e6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .main-banner button:hover {
            background-color: #3cacae;
        }

        .nav-arrows {
            display: flex;
            justify-content: space-between;
            margin: 10px auto;
            max-width: 900px;
        }

        .nav-arrows button {
            background-color: #3cacae;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 18px;
        }

        .nav-arrows button:hover {
            background-color: #5ce1e6;
        }

        /* Facts Section */
        .facts {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 1250px;
            background-color:rgb(167, 229, 232);
        }

        .facts a {
            text-decoration: none;
        }

        .fact-box {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;

        }

        .fact {
            background-color: #3cacae;
            color: white;
            padding: 15px;
            border-radius: 10px;
            flex: 1;
            min-width: 250px;
        }

        .content {
            flex: 1;
            padding: 20px;

        }

        .article-container {
            display: flex;
            flex-wrap: wrap;
            /* Allow wrapping for smaller screens */
            gap: 20px;

        }

        .article {
            flex: 2;
            min-width: 300px;
            text-align: left;
            /* Ensure text is aligned to the left */
            line-height: 1.6;
            /* Improve readability with proper line spacing */
            padding: 15px;
            /* Optional: Add some padding */
            background-color: #ffffff;
            /* Optional: Add a background color for contrast */
            border-radius: 10px;
            /* Optional: Add rounded corners */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Optional: Add a subtle shadow */
        }

        .title h6 {
            color: grey;
        }

        .title h1 {
            font-weight: 900;
            font-family: cursive;
            -webkit-text-stroke: 2px #3cacae;
            color: #5ce1e6;
        }

        /* Comments section */
        .comments {
            flex: 1;
            /* Take up less space compared to article */
            min-width: 300px;
            /* Ensure it doesn't shrink too small */
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Media query for smaller screens */
        @media (max-width: 768px) {
            .article-container {
                flex-direction: column;
                /* Stack items vertically */
            }

            .article,
            .comments {
                flex: 1;
                /* Ensure both take up full width when stacked */
            }
        }

        footer {
            text-align: center;
            background-color: #3cacae;
            color: white;
            padding: 10px 20px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>MINDFUL PATHWAY</h1>
        <div class="menu">
            <a href="#">HOME</a>
            <a href="#">ABOUT</a>
            <a href="articles.html">ARTICLE</a>
            <a href="#">MORE</a>
            <a href="login.html"
                style="background-color: white; color: #3cacae; padding: 5px 10px; border-radius: 5px;">LOGIN</a>
        </div>
    </div>

    <div class="content">
        <div class="facts">
            <div class="article-container">
                <!-- Article Section -->
                <div class="article">
                    <img src="img/<?= $rowList['coverIMG'] ?>">
                    <div class="title">
                        <h1><?= $rowList['title'] ?></h1>
                        <h6>posted on <?= $rowList['timePosted'] ?> by <?= $rowList['username'] ?></h6>
                    </div>
                    <?= $rowList['content'] ?>
                </div>

                <!-- Comments Section -->
                <div class="comments">
                    <h3>Comments</h3>
                    <p>No comments yet. Be the first to share your thoughts!</p>
                    <form>
                        <textarea placeholder="Write a comment..." rows="3"
                            style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;"></textarea>
                        <button type="submit"
                            style="margin-top: 10px; background-color: #3cacae; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                            Post Comment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer>
        &copy; 2024 Mindful Pathway | All Rights Reserved
    </footer>
</body>

</html>