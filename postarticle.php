<?php
session_start();

// Establish a database connection using PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=mindfulpathway', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    header('Location: login.html');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form inputs
    $title = $_POST['title'] ?? null;
    $category = $_POST['category'] ?? null;
    $status = $_POST['status'] ?? 'Pending'; // Default status
    $content = $_POST['content'] ?? null;
    $tags = $_POST['tags'] ?? null; // Optional field
    $summary = $_POST['summary'] ?? 'No summary provided'; // Default summary
    $authorID = $_SESSION['userID'] ?? null; // Assume the user ID is stored in the session

    // Handle missing required fields
    if (empty($title) || empty($category) || empty($content)) {
        echo "<p style='color: red;'>Please fill in all required fields.</p>";
        exit();
    }

    // Handle image upload
    $coverIMG = null;
    if (isset($_FILES['coverIMG']) && $_FILES['coverIMG']['error'] === UPLOAD_ERR_OK) {
        $targetDir = 'uploads/';
        $coverIMG = $targetDir . basename($_FILES['coverIMG']['name']);
        if (!move_uploaded_file($_FILES['coverIMG']['tmp_name'], $coverIMG)) {
            die("<p style='color: red;'>Failed to upload the image.</p>");
        }
    }

    // Insert article into the database
    $sql = "INSERT INTO article (title, category, status, coverIMG, content, tags, summary, timePosted, authorID) 
            VALUES (:title, :category, :status, :coverIMG, :content, :tags, :summary, NOW(), :authorID)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':title' => $title,
            ':category' => $category,
            ':status' => $status,
            ':coverIMG' => $coverIMG,
            ':content' => $content,
            ':tags' => $tags,
            ':summary' => $summary,
            ':authorID' => $authorID,
        ]);
        echo "<p style='color: green;'>Article submitted successfully!</p>";
        header("Location: user_article.php");
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindful Pathway</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Mindful Pathway | User Dashboard</title>
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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
            <img src="uploads/<?php echo isset($_SESSION['img_Profile']) ? $_SESSION['img_Profile'] : 'default_profile.jpg'; ?>"
                alt="Profile" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 70px;">
        </div>

        <!-- Notifications Dropdown -->
        <div id="notifications-dropdown" style="display: none; position: absolute; right: 20px; top: 60px; background-color: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); width: 300px; max-height: 400px; overflow-y: auto;">
            <h5 style="background-color: #3cacae; color: white; padding: 10px; margin: 0; border-radius: 8px 8px 0 0;">Notifications</h5>
            <?php if (empty($notifications)): ?>
                <p style="padding: 10px; color: #666;">No new notifications.</p>
            <?php else: ?>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php foreach ($notifications as $notification): ?>
                        <li style="padding: 10px; border-bottom: 1px solid #ddd;">
                            <p style="margin: 0;"><?php echo htmlspecialchars($notification['message']); ?></p>
                            <small style="color: grey;"><?php echo $notification['timePosted']; ?></small>
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
        <a href="profile.php">My Profile</a>
        <a href="articles.php">Article</a>
        <a href="feedback.html" class="active">Feedback</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">


        <div class="banner">

            <div class="content">

                <section class="form-section">
                    <form id="article-form" action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <header>
                                <h1 style="padding-top: 22px;">ARTICLES</h1>
                            </header>
                            <label for="title">Article Title</label>
                            <input type="text" id="title" name="title" placeholder="Enter the title of your article">

                            <div class="form-group">
                                <label for="category">Category or Topic</label>
                                <select id="category" name="category" class="form-control">
                                    <option value="" disabled selected>--Select a Category--</option>
                                    <option value="mental-health-and-wellness">Mental Health and Wellness</option>
                                    <option value="self-care">Self-Care</option>
                                    <option value="personal-growth">Personal Growth</option>
                                    <option value="psychology">Psychology</option>
                                    <option value="health-and-fitness">Health and Fitness</option>
                                </select>

                                <div class="form-group">
                                    <label for="content">Article Content</label>
                                    <textarea id="content" name="content" class="form-control" rows="5" placeholder="Write your article here..."></textarea>

                                    <div class="form-group">
                                        <label for="tags">Tags (Optional)</label>
                                        <input type="text" id="tags" name="tags" class="form-control" placeholder="e.g., mental health, self-care, psychology">

                                        <div class="form-group">
                                            <label for="coverIMG">Upload a Featured Image (Optional)</label>
                                            <input type="file" id="coverIMG" name="coverIMG" class="form-control">

                                            <div class="form-group">
                                                <label for="summary">Short Summary of Your Article (Optional)</label>
                                                <textarea id="summary" name="summary" class="form-control" rows="2" placeholder="Provide a brief summary or introduction (1-2 sentences)."></textarea>

                                                <button type="submit" id="submit-btn" class="submit-btn">
                                                    <i class="fas fa-paper-plane"></i> SUBMIT
                                                </button>
                    </form>
                    <p id="confirmation-message" class="hidden">Done Submit!</p>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </section>
    </div>
    </div>
    <footer>
        <p>© 2024 Mindful Pathway | All Rights Reserved</p>
    </footer>
    </div>
    </div>
    <script src="script1.js"></script>
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