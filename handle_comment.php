<?php
session_start();

$conn = new mysqli("localhost", "root", "", "mindfulpathway");
if ($conn->connect_errno) {
    echo "Failed to Open Database: " . $conn->connect_error;
    exit();
}

// Authentication Check
if (!isset($_SESSION['adminID'])) {
    header('Location: login.php');
    exit();
}
$adminID = $_SESSION['adminID'];

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $imgProfile = $_FILES['imgProfile'];

    // Image upload handling
    if ($imgProfile['size'] > 0 && $imgProfile['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . uniqid() . "-" . basename($imgProfile['name']);
        if (!move_uploaded_file($imgProfile['tmp_name'], $targetFile)) {
            echo "<script>alert('Failed to upload the image.');</script>";
        } else {
            $uploadedImage = $targetFile;
        }
    } else {
        $uploadedImage = $_POST['existingImgProfile'] ?? 'uploads/default-profile.png';
    }

    // Update admin profile
    $query = "UPDATE admin SET email = ?, bio = ?, imgProfile = ? WHERE adminID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $email, $bio, $uploadedImage, $adminID);
    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!');</script>";
    } else {
        echo "<script>alert('Failed to update profile.');</script>";
    }
}

// Fetch user details
$query = "SELECT * FROM admin WHERE adminID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $adminID);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Fetch articles
$pending_query = "SELECT article.*, user.username FROM article LEFT JOIN user ON article.authorID = user.userID WHERE article.status IS NULL ORDER BY article.timePosted DESC";
$approved_query = "SELECT article.*, user.username FROM article LEFT JOIN user ON article.authorID = user.userID ORDER BY article.timePosted DESC";

$pending_result = $conn->query($pending_query);
$approved_result = $conn->query($approved_query);

$pending_articles = $pending_result->fetch_all(MYSQLI_ASSOC);
$approved_articles = $approved_result->fetch_all(MYSQLI_ASSOC);

// Approve or Reject articles
if (isset($_POST['Approve']) || isset($_POST['Reject'])) {
    $articleID = $_POST['articleID'];
    $status = isset($_POST['Approve']) ? 'Approved' : 'Rejected';

    $update_query = "UPDATE article SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $articleID);
    $stmt->execute();
}

require 'dbconnect.php';
$qArticles = query("SELECT * FROM article");

// Get the project ID from the URL parameter
$xId = $_GET['articleID'];

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
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Management | Mindful Pathway </title>
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding-top: 60px;
        }

        /* Header */
        .header {
            background-color: #3cacae;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .header .logo {
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .header .logo img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        /* Search Bar */
        .search-bar {
            display: flex;
            align-items: center;
            position: relative;
        }

        .search-bar input {
            width: 300px;
            padding: 8px;
            border-radius: 20px;
            border: 1px solid #ccc;
        }

        .search-bar button {
            position: absolute;
            right: 10px;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        /* Sidebar */
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #3ea3a4;
            padding-top: 60px;
            z-index: 500;
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: background-color 0.3s ease;
            text-align: center;
            margin-bottom: 10px;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .sidebar .title {
            font-size: 24px;
            padding-left: 20px;
            margin-bottom: 30px;
            margin-top: 20px;
        }

        .sidebar .active {
            background-color: #5ce1e6;
        }

        .sidebar .logout {
            background-color: #5ce1e6;
            color: #333;
            width: 80%;
            text-align: center;
            padding: 10px 10px;
            border-radius: 25px;
            margin: 20px auto 0;
            margin-top: 80px;
        }

        .sidebar .logout:hover {
            background-color: #b1fcff;
        }

        /* Main Content Area */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .main-content h1 {
            font-size: 34px;
            font-weight: bold;
            text-align: left;
            color: rgb(0, 0, 0);
            margin-top: 4px;
            margin-bottom: 0;
            text-shadow: 2px 2px 2px #00000066;
        }

        .main-content i {
            font-size: 15px;
            text-align: left;
            color: rgb(0, 0, 0);
            display: block;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .banner {
            width: 100%;
            height: 300px;
            background-image: url('img/banner1.png');
            background-size: cover;
            background-position: center;
            margin-bottom: 30px;
        }


        footer {
            text-align: center;
            background-color: #3cacae;
            color: white;
            padding: 15px;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 2;
        }

        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #359799;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 20px;
            cursor: pointer;
            display: none;
            z-index: 1000;
        }

        .back-to-top:hover {
            background-color: #5ce1e6;
        }

        .admin-section {
            margin-top: 30px;
        }

        .admin-card {
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .admin-card h2 {
            font-size: 24px;
        }

        .admin-card p {
            font-size: 16px;
        }

        .admin-btn {
            padding: 10px 20px;
            background-color: #3ea3a4;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .admin-btn:hover {
            background-color: #359799;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #3ea3a4;
            color: white;
        }

        /* Approve and Reject Buttons */
        .btn {
            padding: 5px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            margin-right: 10px;
        }

        .btn-success {
            background-color: #4CAF50;
            color: white;
        }

        .btn-success:hover {
            background-color: #45a049;
        }

        .btn-danger {
            background-color: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background-color: #da190b;
        }

        .search-bar {
            margin-bottom: 20px;
            align-items: center;
        }

        .article-link {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
            transition: color 0.3s, transform 0.2s;
        }

        .article-link:hover {
            color: #0056b3;
            transform: scale(1.1);
        }

        .article-link i {
            margin-left: 5px;
            font-size: 12px;
        }

        .hamburger {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: white;
            cursor: pointer;
            margin-right: 20px;
        }

        .hamburger span {
            display: block;
            background-color: white;
            height: 2px;
            width: 20px;
            margin: 5px auto;
            transition: 0.3s;
        }

        /* Desktop View */
        @media (min-width: 769px) {
            .sidebar {
                display: block;
            }

            .hamburger {
                display: none;
            }

            .main-content {
                margin-left: 250px;
            }
        }

        /* Responsive: Mobile View */
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .hamburger {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

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


        .facts {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 1000px;
            background-color: rgb(167, 229, 232);
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
            justify-content: flex-end;
        }

        .article-container {
            display: flex;
            flex-wrap: wrap;
            /* Allow wrapping for smaller screens */
            gap: 20px;
            justify-content: flex-end;
            /* Align content to the right */
        }


        .article img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            /* Optional: add a slight rounding to match the article's border radius */
            display: block;
            margin-bottom: 15px;
            /* Optional: add some spacing below the image */
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
            font-size: 28px;
            font-weight: bold;
            text-align: left;
            color: rgb(0, 0, 0);
            margin-top: 4px;
            margin-bottom: 0;
            text-shadow: 2px 2px 2px #00000066;
        }

        .comments {
            flex: 1;
            min-width: 300px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .comments a {
            color: #3cacae;
            text-decoration: none;
        }

        .comments a:hover {
            text-decoration: underline;
        }

        .comments form {
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="logo">
            <img src="img/favicon.png" alt="Logo">
            <span>Mindful Pathway</span>
        </div>
        <div class="menu">
            <i class="fas fa-bell" style="font-size: 20px; margin-right: 20px;" onclick="showNotifications()"></i>
            <img src="<?php echo !empty($admin['imgProfile']) ? htmlspecialchars($admin['imgProfile']) : 'uploads/default-profile.png'; ?>"
                alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 20px;">
        </div>

        <div class="hamburger" onclick="toggleSidebar()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <div class="sidebar">
        <div class="title"><?php echo "Welcome, " . htmlspecialchars($admin['username']); ?></div>
        <a href="admin_home.php">Home</a>
        <a href="admin_about.php">About</a>
        <a href="admin_profile.php">My Profile</a>
        <a href="article_manage.php" class="active">Manage Articles</a>
        <a href="admin_user_manage.php">Manage Users</a>
        <a href="admin_feedback.php">Feedback</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <h1>Manage Comments Section</h1>
        <i>Here you can manage any inappropiate comments posted by users. You can delete the comment to avoid unneccesary behaviors.</i>

        <div class="content">
            <div class="facts">
                <div class="article-container">
                    <!-- Article Section -->
                    <div class="article">
                        <?php
                        // Check if coverIMG is null or empty, and use a default image if needed
                        $coverIMG = !empty($rowList['coverIMG']) ? $rowList['coverIMG'] : 'defaultIMG.png'; // Use just the file name
                        ?>
                        <img src="img/<?= htmlspecialchars($coverIMG) ?>" alt="Article Cover">
                        <div class="title">
                            <h1><?= htmlspecialchars($rowList['title']) ?></h1>
                            <h6>Posted on <?= htmlspecialchars($rowList['timePosted']) ?> by <?= htmlspecialchars($rowList['username']) ?></h6>
                        </div>
                        <div class="content">
                            <?= htmlspecialchars($rowList['content']) ?>
                        </div>
                    </div>
                    <?php
                    $commentsQuery = "
                        SELECT 
                            comment.commentID, 
                            comment.content, 
                            comment.timePosted, 
                            comment.parentID, 
                            user.username 
                        FROM 
                            comment 
                        JOIN 
                            user ON comment.userID = user.userID 
                        WHERE 
                            comment.articleID = '$xId' 
                        ORDER BY 
                            comment.parentID ASC, comment.timePosted DESC";
                    $commentsResult = mysqli_query($dbc, $commentsQuery);

                    $comments = [];
                    while ($row = mysqli_fetch_assoc($commentsResult)) {
                        $comments[] = $row;
                    }

                    function displayComments($comments, $parentID = NULL, $level = 0)
                    {
                        foreach ($comments as $comment) {
                            if ($comment['parentID'] == $parentID) {
                                // Indent replies based on nesting level
                                echo '<div style="margin-left: ' . (20 * $level) . 'px; padding: 10px; border-left: 2px solid #ccc;">';
                                echo '<strong>' . htmlspecialchars($comment['username']) . '</strong>: ' . htmlspecialchars($comment['content']);
                                echo '<br><small style="color: grey; font-size: 0.8em;">Posted on ' . htmlspecialchars($comment['timePosted']) . '</small>';


                                echo '<a href="delete_comment.php?comment_id=' . htmlspecialchars($comment['commentID'], ENT_QUOTES, 'UTF-8') . '" 
                                        onclick="return confirm(\'Are you sure you want to delete this comment and all its replies?\')" 
                                        style="color: red; text-decoration: none; margin-left: 10px;">Delete</a>';



                                echo '</div>';
                                // Recursive call for child comments
                                displayComments($comments, $comment['commentID'], $level + 1);
                            }
                        }
                    }
                    ?>
                    <!-- Comments Section -->
                    <div class="comments">
                        <h3>Comments</h3>
                        <?php
                        if (!empty($comments)) {
                            displayComments($comments);
                        } else {
                            echo '<p>No comments yet. Be the first to share your thoughts!</p>';
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Footer -->
    <footer>
        &copy; 2024 Mindful Pathway | All Rights Reserved
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" onclick="scrollToTop()">↑</button>

    <script>
        function showNotifications() {
            alert("You have no new notifications.");
        }

        // Scroll to top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Function to handle article approval
        function approveArticle(articleID) {
            if (confirm("Are you sure you want to approve this article?")) {
                alert("Article " + articleID + " approved!");
            }
        }
        const searchInput = document.getElementById('searchInput');
        const articleTable = document.getElementById('articleTable');

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = articleTable.getElementsByTagName('tr');
            Array.from(rows).forEach(row => {
                const titleCell = row.getElementsByTagName('td')[0];
                if (titleCell) {
                    const titleText = titleCell.textContent || titleCell.innerText;
                    row.style.display = titleText.toLowerCase().includes(filter) ? '' : 'none';
                }
            });
        });

        function toggleSidebar() {
            var sidebar = document.querySelector('.sidebar');
            if (sidebar.style.display === 'none' || sidebar.style.display === '') {
                sidebar.style.display = 'block';
            } else {
                sidebar.style.display = 'none';
            }
        }
        window.addEventListener('resize', function() {
            var sidebar = document.querySelector('.sidebar');
            if (window.innerWidth > 768) {
                sidebar.style.display = 'block';
            } else {
                sidebar.style.display = 'none';
            }
        });
    </script>
</body>

</html>