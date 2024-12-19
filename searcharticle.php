<?php
include "DBConnect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $searchQuery = mysqli_real_escape_string($dbc, $_POST['query']); // Sanitize input to prevent SQL injection

    // Query the database for matching articles
    $sql = "SELECT article.*, user.username 
            FROM article 
            JOIN user ON article.authorID = user.userID
            WHERE article.title LIKE '%$searchQuery%' OR article.content LIKE '%$searchQuery%'
            ORDER BY article.timePosted DESC";

    $result = mysqli_query($dbc, $sql);
    $articles = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_close($dbc);
} else {

    echo "<script>
        alert('Server Error Searching.');
        window.location.href = 'articles.php'; 
    </script>";
    exit; // Ensure the script stops executing after the redirect


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
            max-width: 900px;
            text-align: center;
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

            a {
                text-decoration: none;
                color: white;
            }
        }

        .search-results {
            margin: 20px auto;
            max-width: 900px;
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
            <a href="article.html">ARTICLE</a>
            <a href="#">MORE</a>
            <a href="login.html"
                style="background-color: white; color: #3cacae; padding: 5px 10px; border-radius: 5px;">LOGIN</a>
        </div>
    </div>

    <div class="content">
        <div class="facts ">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 70%; vertical-align: top; text-align: left; padding-right: 50px;">
                        <h2>Search Results</h2>
                        <p>Searching for "<?php echo $searchQuery ?>"</p>
                    </td>
                    <td style="width: 30%; vertical-align: top; text-align: left;">
                        What do you want to learn today?
                        <form action="searcharticle.php" method="post">
                            <div style="display: flex; align-items: center;">
                                <input type="text" name="query" placeholder="Search" required
                                    style="flex: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px;">
                                <button type="submit"
                                    style="margin-left: 10px; background-color: #3cacae; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                                    >
                                </button>
                            </div>
                        </form>
                        <a class="btn btn-primary" href="#" role="button" style="background-color: #3cacae;
                                margin: 15px; border-color: #ffffff;">POST ARTICLE</a>
                    </td>
                </tr>
            </table>


            <div class="fact-box">
                <div class="search-results">
                    <?php if (!empty($articles)): ?>
                        <?php foreach ($articles as $article): ?>
                            <div class="fact">
                                <h3><a href="readarticle.php?id=<?= $article['articleID'] ?>" style="text-decoration: none;"><?= $article['title'] ?></a></h3>
                                <p style="color:#ccc;"><strong>Posted on:</strong> <?= $article['timePosted'] ?> by <?= $article['username'] ?></p>
                                <p><?= substr($article['content'], 0, 500) ?>...</p>
                                <a href="readarticle.php?id=<?= $article['articleID'] ?>">Read more</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-results">
                            <p>No articles found for your search: <strong><?= htmlspecialchars($searchQuery) ?></strong></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="fact">
                    The Impact of Sleep on Mental Health: Poor sleep can contribute to or worsen mental health issues.
                    Lack
                    of sleep affects brain function, emotional regulation, and decision-making, increasing the
                    likelihood of
                    experiencing conditions like anxiety and depression.
                </div>
                <div class="fact">
                    Cognitive Behavioral Therapy (CBT): CBT is one of the most effective therapeutic approaches for
                    treating
                    mental health conditions like depression, anxiety, and PTSD. It focuses on changing negative thought
                    patterns and behaviors to improve emotional well-being.
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