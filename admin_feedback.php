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
        if (move_uploaded_file($imgProfile['tmp_name'], $targetFile)) {
            $uploadedImage = $targetFile;
        } else {
            echo "<script>alert('Failed to upload the image.');</script>";
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
    
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header('Location: login.php');
        exit();
    }
    $admin = $result->fetch_assoc();
} else {
    header('Location: login.php');
    exit();
}

$stmt = $conn->prepare("
    SELECT article.articleID, article.title, article.timePosted, article.status, user.username 
    FROM article 
    JOIN user ON article.authorID = user.userID 
    ORDER BY timePosted DESC LIMIT 5
");
$stmt->execute();
$result = $stmt->get_result();

$articles = [];
while ($row = $result->fetch_assoc()) {
    $row['status'] = $row['status'] ?: 'Pending';
    $articles[] = $row;
}


// Fetch Users (Preview)
$stmt_users = $conn->prepare("SELECT userID, username, bio, email FROM user ORDER BY username ASC LIMIT 5");
$stmt_users->execute();
$result_users = $stmt_users->get_result();

$users = [];
while ($row = $result_users->fetch_assoc()) {
    $users[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Feedback</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background-color: #39B7B7;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .sidebar .logo {
            padding: 20px;
            text-align: center;
        }

        .sidebar .logo img {
            width: 50px;
            margin-bottom: 10px;
        }

        .sidebar .logo h1 {
            font-size: 18px;
            font-weight: 700;
        }

        .sidebar nav a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
        }

        .sidebar nav a:hover, .sidebar nav a.active {
            background-color: #2a9a9a;
        }

        .sidebar .logout {
            padding: 15px 20px;
            text-align: center;
            background-color: #2a9a9a;
            cursor: pointer;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #39B7B7;
            padding: 15px;
            color: white;
        }

        .header .title {
            font-size: 18px;
            font-weight: 700;
        }

        .content {
            padding: 20px;
            overflow-y: auto;
        }

        .content h2 {
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 24px;
        }

        .feedback-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .feedback-table th,
        .feedback-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .feedback-table th {
            background-color: #39B7B7;
            color: white;
        }

        .feedback-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .feedback-form h3 {
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: 700;
        }

        .feedback-form textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .feedback-form button {
            width: 100%;
            padding: 10px;
            background-color: #39B7B7;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
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
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="img/logo.png" alt="Logo">
            <h1>Admin Panel</h1>
        </div>
          <!-- Sidebar -->
    <div class="sidebar">
      <div class="title"><?php echo "Welcome, " . htmlspecialchars($username); ?></div>
      <a href="admin_home.php" >Home</a>
      <a href="admin_about.php">About</a>
      <a href="admin_profile.php">My Profile</a>
      <a href="article_manage.php">Manage Articles</a>
      <a href="admin_user_manage.php">Manage Users</a>
      <a href="admin_feedback.php"class= active">Feedback</a>
      <a href="logout.php" class="logout">Logout</a>

    <div class="main-content">
        <div class="header">
            <div class="title">Manage Feedback</div>
        </div>
        <div class="content">
            <table class="feedback-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Submitted Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>User Feedback 1</td>
                        <td>Admin</td>
                        <td>2025-01-01</td>
                        <td>Approved</td>
                    </tr>
                    <tr>
                        <td>User Feedback 2</td>
                        <td>Admin</td>
                        <td>2024-12-15</td>
                        <td>Pending</td>
                    </tr>
                </tbody>
            </table>

            <div class="feedback-form">
                <h3>Submit Admin Feedback</h3>
                <form method="POST" action="submit_feedback.php">
                    <textarea name="feedback" placeholder="Write your feedback here..." required></textarea>
                    <button type="submit">Submit</button>
                </form>
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
    </div>
</body>
</html>
