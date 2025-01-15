<?php
// Start session
session_start();

// Database connection
$dbc = new mysqli("localhost", "root", "", "mindfulpathway");
if ($dbc->connect_errno) {
    echo "Failed to Open Database: " . $dbc->connect_error;
    exit();
}

// Authentication Check
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

$userID = $_SESSION['userID'];

$query = "SELECT * FROM user WHERE userID = ?";
$stmt = $dbc->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Set variables
$username = $user['username'] ?? 'Guest'; // Default to 'Guest' if username is null
$email = $user['email'] ?? '';
$bio = $user['bio'] ?? '';
$imgProfile = $user['imgProfile'] ?? 'uploads/default-profile.png';

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $imgProfile = $_FILES['imgProfile'];

    // Image upload handling
    if ($imgProfile['size'] > 0 && $imgProfile['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . uniqid() . "-" . basename($imgProfile['name']);
        if (move_uploaded_file($imgProfile['tmp_name'], $targetFile)) {
            $uploadedImage = $targetFile;
        } else {
            echo "<script>alert('Failed to upload the image.');</script>";
        }
    } else {
        $uploadedImage = $_POST['existingImgProfile'] ?? 'uploads/default-profile.png';
    }

    // Update user profile
    $query = "UPDATE user SET email = ?, bio = ?, imgProfile = ? WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $email, $bio, $uploadedImage, $userID);
    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!');</script>";
    } else {
        echo "<script>alert('Failed to update profile.');</script>";
    }
}

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
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindful Pathway | The Article </title>
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
        }

        /* Notifications Dropdown */
        #notifications-dropdown {
            display: none;
            /* Initially hidden */
            position: absolute;
            right: 20px;
            top: 60px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            /* Ensure scrolling for large content */
            z-index: 1000;
            /* Make sure it's on top */
        }

        /* Adjust padding and styles inside the dropdown */
        #notifications-dropdown ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #notifications-dropdown li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }

        #notifications-dropdown li:last-child {
            border-bottom: none;
            /* Remove border for the last item */
        }

        #notifications-dropdown li:hover {
            background-color: #f5f5f5;
            /* Add a hover effect for better UX */
        }

        #notifications-dropdown h5 {
            margin: 0;
            padding: 10px;
            background-color: #3cacae;
            color: white;
            border-radius: 8px 8px 0 0;
            font-size: 16px;
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
            font-size: 34px;
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
        <div class="logo">
            <img src="img/favicon.png" alt="Logo">
            <span>Mindful Pathway</span>
        </div>
        <div class="menu">
            <i class="fas fa-bell" style="font-size: 20px; margin-right: 20px;" onclick="showNotifications()"></i>
            <img src="<?php echo !empty($user['imgProfile']) ? htmlspecialchars($user['imgProfile']) : 'uploads/default-profile.png'; ?>"
                alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 20px;">
        </div>

        <!-- Notifications Dropdown -->
        <div id="notifications-dropdown" style="display: none; position: absolute; right: 20px; top: 60px; background-color: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); width: 300px; max-height: 400px; overflow-y: auto;">
            <h5 style="background-color: #3cacae; color: white; padding: 10px; margin: 0; border-radius: 8px 8px 0 0;">Notifications</h5>
            <?php if (empty($notifications)): ?>
                <p style="padding: 10px; color: #666;">No new notifications.</p>
            <?php else: ?>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php foreach ($notifications as $notification): ?>
                        <li>
                            <a href="readarticle.php?id=<?php echo htmlspecialchars($notification['articleID']); ?>"
                                onclick="markAsRead(<?php echo $notification['notificationID']; ?>)"
                                style="text-decoration: none; color: black;">
                                <p style="margin: 0;"><?php echo htmlspecialchars($notification['messages']); ?></p>
                                <small style="color: grey;"><?php echo $notification['timePosted']; ?></small>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

        </div>

        <div class="hamburger" onclick="toggleSidebar()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="title"><?php echo "Welcome, " . htmlspecialchars($username); ?></div>
        <a href="user_home.php">Home</a>
        <a href="user_about.php">About</a>
        <a href="user_profile.php">My Profile</a>
        <a href="user_article.php" class="active">Article</a>
        <a href="user_feedback.php">Feedback</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

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
                            comment.userID,
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

                            // Reply link and hidden form
                            echo '<a href="javascript:void(0);" onclick="toggleReplyForm(' . $comment['commentID'] . ')" style="color: #3cacae; text-decoration: none; margin-left: 10px;">Reply</a>';
                            // Delete link
                            $currentUserID = $_SESSION['userID'];
                            if ($comment['userID'] == $currentUserID) {
                                // Display the "Delete" button
                                echo '<a href="user_delete_comment.php?comment_id=' . htmlspecialchars($comment['commentID']) . '" 
                                onclick="return confirm(\'Are you sure you want to delete this comment and all its replies?\')" 
                                style="color: red; text-decoration: none; margin-left: 10px;">Delete</a>';
                            }
                            echo '<form id="reply-form-' . $comment['commentID'] . '" action="add_comment.php" method="post" style="display: none; margin-top: 5px;">';
                            echo '<input type="hidden" name="article_id" value="' . htmlspecialchars($_GET['id']) . '">';
                            echo '<input type="hidden" name="parent_id" value="' . htmlspecialchars($comment['commentID']) . '">';
                            echo '<textarea name="comment_content" rows="2" placeholder="Reply to this comment..." 
                            style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;"></textarea>';
                            echo '<button type="submit" style="margin-top: 10px; background-color: #3cacae; color: white; border: none; border-radius: 5px; cursor: pointer;">Reply</button>';
                            echo '</form>';

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
                    <form action="add_comment.php" method="post">
                        <textarea name="comment_content" placeholder="Write a comment..." rows="3"
                            style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;"></textarea>
                        <input type="hidden" name="article_id" value="<?= htmlspecialchars($xId) ?>">
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

    <!-- Back to Top Button -->
    <button class="back-to-top" onclick="scrollToTop()">↑</button>

    <script>
        // Close the search bar
        function closeSearch() {
            document.getElementById('search-input').value = '';
        }

        // Scroll to top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function showNotifications() {
            var dropdown = document.getElementById("notifications-dropdown");
            if (dropdown.style.display === "none" || dropdown.style.display === "") {
                dropdown.style.display = "block";
            } else {
                dropdown.style.display = "none";
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener("click", function(event) {
            var dropdown = document.getElementById("notifications-dropdown");
            var bellIcon = document.querySelector(".fas.fa-bell");
            if (!dropdown.contains(event.target) && event.target !== bellIcon) {
                dropdown.style.display = "none";
            }
        });

        function markAsRead(notificationID) {
            // Make an AJAX request to mark the notification as read
            $.ajax({
                url: 'noti_mark_as_read.php', // PHP script to mark as read
                method: 'POST',
                data: {
                    notificationID: notificationID
                },
                success: function(response) {
                    console.log('Notification marked as read:', response);
                },
                error: function() {
                    console.error('Error marking notification as read.');
                }
            });
        }



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
    <script>
        (function() {
            if (!window.chatbase || window.chatbase("getState") !== "initialized") {
                window.chatbase = (...arguments) => {
                    if (!window.chatbase.q) {
                        window.chatbase.q = []
                    }
                    window.chatbase.q.push(arguments)
                };
                window.chatbase = new Proxy(window.chatbase, {
                    get(target, prop) {
                        if (prop === "q") {
                            return target.q
                        }
                        return (...args) => target(prop, ...args)
                    }
                })
            }
            const onLoad = function() {
                const script = document.createElement("script");
                script.src = "https://www.chatbase.co/embed.min.js";
                script.id = "Bim8_kBed-XDQ_TodjahJ";
                script.domain = "www.chatbase.co";
                document.body.appendChild(script)
            };
            if (document.readyState === "complete") {
                onLoad()
            } else {
                window.addEventListener("load", onLoad)
            }
        })();
    </script>
    <script>
        function toggleReplyForm(commentID) {
            const form = document.getElementById(`reply-form-${commentID}`);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>


</body>

</html>