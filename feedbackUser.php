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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #3cacae;
            color: white;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .logo span {
            font-weight: bold;
            font-size: 20px;
        }

        .menu {
            display: flex;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .menu li {
            margin: 0 10px;
        }

        .menu a {
            text-decoration: none;
            color: white;
            font-weight: bold;
        }

        .hamburger {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            margin-right: 20px;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 50px;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: #3cacae;
            color: white;
            padding-top: 20px;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            z-index: 999;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            padding: 15px 20px;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            font-size: 18px;
        }

        .sidebar ul li a:hover {
            background-color: #2b8c8b;
            border-radius: 5px;
        }

        /* Main Content */
        .main-content {
            margin-top: 80px;
            padding: 20px;
            margin-left: 250px; /* Sidebar width */
            transition: margin-left 0.3s;
        }

        .main-content.collapsed {
            margin-left: 0;
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .feedback-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .feedback-card {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            flex: 1 1 calc(50% - 20px);
            max-width: calc(50% - 20px);
        }

        .feedback-card h3 {
            margin: 0 0 10px;
            color: #3cacae;
        }

        .feedback-card p {
            margin: 0;
            color: #555;
        }

        .feedback-form {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            flex: 1 1 100%;
        }

        .feedback-form textarea {
            width: 100%;
            height: 100px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .submit-btn {
            background-color: #3cacae;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #2b8c8b;
        }

        /* Footer */
        footer {
            background-color: #3cacae;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .menu {
                display: none;
            }

            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 0;
            }

            .feedback-container {
                flex-direction: column;
            }

            .feedback-card {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .feedback-card {
                padding: 15px;
            }

            .feedback-form {
                padding: 15px;
            }

            footer {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <button class="hamburger" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <img src="img/favicon.png" alt="Logo">
            <span>Mindful Pathway</span>
        </div>
        <ul class="menu">
            <li><a href="#" class="notification"><i class="fas fa-bell"></i></a></li>
            <li><a href="profile.html" class="profile"><i class="fas fa-user-circle"></i></a></li>
        </ul>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <ul>
            <li><a href="index.html">DASHBOARD</a></li>
            <li><a href="about.html">ABOUT</a></li>
            <li><a href="profile.html">MY PROFILE</a></li>
            <li><a href="articles.html">ARTICLE</a></li>
            <li><a href="feedback.html">FEEDBACK</a></li>
            <li><a href="logout.html">LOG OUT</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="content">
        <h1>FEEDBACK FROM OUR COMMUNITY</h1>
        <div class="feedback-container">
            <div class="feedback-card">
                <h3>Sarah Ali</h3>
                <p>Posted on: 2024-10-21</p>
                <p>This article was very informative. I learned a lot about mindfulness and how it can help in daily life. Thank you for sharing!</p>
            </div>
            <div class="feedback-card">
                <h3>Harris Shuaib</h3>
                <p>Posted on: 2024-09-09</p>
                <p>I really appreciate how you explained the steps to manage anxiety. I will definitely try some of these techniques!</p>
            </div>
            <div class="feedback-form">
                <h3>Your Thoughts Matter for Us!</h3>
                <textarea placeholder="Write your feedback here..."></textarea>
                <button class="submit-btn">Submit Feedback</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2024 Mindful Pathway | All Rights Reserved
    </footer>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('active');
            content.classList.toggle('collapsed');
        }
    </script>
</body>
</html>
