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

// Fetch unread notifications for the logged-in user
$userID = $_SESSION['userID']; // Assuming userID is stored in the session
$notifications = [];
$queryN = "SELECT * FROM notifications WHERE userID = '$userID'  ORDER BY timePosted DESC";
$resultN = mysqli_query($dbc, $queryN);

if ($resultN) {
    $notifications = mysqli_fetch_all($resultN, MYSQLI_ASSOC);
} else {
    echo "Error fetching notifications: " . mysqli_error($dbc);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindful Pathway | Daily Articles</title>
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
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
            margin-top: 50px;
            max-width: 900px;
            text-align: center;
        }

        .title a {
            text-decoration: none;
            font-size: 20px;

        }

        .detail {
            color: lightgray;
        }

        .content {
            font-size: 12px;

            a {
                text-decoration: none;
                color: white;
            }
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
            min-width: 400px;
        }

        .fact img {
            width: 350px;
            height: auto;
        }

        .content {
            flex: 1;
            padding: 20px;

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
        <a href="user_home.php" class="active">Home</a>
        <a href="user_about.php">About</a>
        <a href="user_profile.php">My Profile</a>
        <a href="user_article.php">Article</a>
        <a href="user_feedback.php">Feedback</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="content">
        <div class="facts ">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 70%; vertical-align: top; text-align: left; padding-right: 50px;">
                        <h2>Article : Mental Health Awareness</h2>
                        <p>"The more that you read, the more things you will know. The more that you learn, the more places you'll go." — Dr. Seuss</p>
                    </td>
                    <td style="width: 30%; vertical-align: top; text-align: left;">
                        <i>What do you want to learn today?</i>
                        <form action="searcharticle.php" method="post" style="margin-bottom: 10px;">
                            <div style="display: flex; align-items: center;">
                                <input type="text" name="query" placeholder="Search" required
                                    style="flex: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px;">
                                <button type="submit"
                                    style="margin-left: 10px; background-color: #3cacae; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                                    >
                                </button>
                            </div>
                        </form>
                        <a href="postarticle.php" role="button"
                            style="display: inline-block; background-color: #3cacae; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                            POST ARTICLE
                        </a>
                    </td>
                </tr>
            </table>



            <div class="fact-box">
                <?php
                include "DBConnect.php";

                // Enable error reporting for debugging
                error_reporting(E_ALL);
                ini_set('display_errors', 1);

                $sql = "SELECT article.*, user.username 
        FROM article 
        JOIN user ON article.authorID = user.userID
        ORDER BY timePosted";

                $result = mysqli_query($dbc, $sql);

                // Check if the query executed successfully
                if (!$result) {
                    die("Query failed: " . mysqli_error($dbc));
                }

                // Check if articles exist in the database
                if ($result && mysqli_num_rows($result) > 0) {
                    $articles = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    
                    foreach ($articles as $article) {
                        // Prepare variables, handling NULL values gracefully
                        $title = htmlspecialchars($article['title'] ?? 'Untitled');
                        $timePosted = htmlspecialchars($article['timePosted'] ?? 'Unknown');
                        $username = htmlspecialchars($article['username'] ?? 'Anonymous');
                        $contentPreview = htmlspecialchars(substr($article['content'] ?? '', 0, 500));
                        
                        echo '<div class="fact">';
                        
                        // Only display the image if coverIMG is not NULL
                        if (!empty($article['coverIMG'])) {
                            echo '<img src="img/' . htmlspecialchars($article['coverIMG']) . '" >';
                        }
                        
                        echo '    <div class="title">
                                    <a href="readarticle.php?id=' . htmlspecialchars($article['articleID']) . '">' . $title . '</a>
                                </div>
                                <div class="detail">
                                    ' . $timePosted . ', by ' . $username . '
                                </div>
                                <div class="content">
                                    ' . $contentPreview . '...
                                    <a href="readarticle.php?id=' . htmlspecialchars($article['articleID']) . '">See more</a>
                                </div>
                            </div><br>';
                    }


                } else {
                    echo "No Articles found."; // Display a message if the database is empty
                }

                // Free the result set and close the connection
                mysqli_free_result($result);
                mysqli_close($dbc);
                ?>


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

</body>

</html>