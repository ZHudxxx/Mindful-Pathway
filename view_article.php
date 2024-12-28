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
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $stmt = $dbc->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleID = intval($_POST['articleID']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $status = 'approved';
    } elseif ($action === 'reject') {
        $status = 'rejected';
    } else {
        echo "Invalid action.";
        exit();
    }

    $stmt = $dbc->prepare("UPDATE article SET status = ? WHERE articleID = ?");
    $stmt->bind_param("si", $status, $articleID);

    if ($stmt->execute()) {
        echo "Article status updated successfully.";
    } else {
        echo "Failed to update article status.";
    }

    $stmt->close();
    $dbc->close();

    header("Location: admin_home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> | Mindful Pathway</title>
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
 .main-content {
            margin-left: 250px;
            padding: 20px;
        }
     .main-content h1 {
            font-size: 34px;
            font-weight: bold;
            text-align: center;
            color: rgb(0, 0, 0);
            margin: 20px 0;
            font-family: "Times New Roman", Times, serif;
        }

.main-content i {
  font-size: 15px;
  text-align: left;
  color: rgb(0, 0, 0);
  display: block; 
  margin-top: 0;
  margin-bottom: 10px;
}
        .content {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-family: "Times New Roman", Times, serif;
        }

        .content .metadata {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn.approve {
            background-color: #4caf50;
            color: white;
        }

        .btn.approve:hover {
            background-color: #45a049;
        }

        .btn.reject {
            background-color: #f44336;
            color: white;
        }

        .btn.reject:hover {
            background-color: #e41f1f;
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
        .back-button {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #3cacae;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #359799;
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
    <img src="uploads/<?php echo isset($_SESSION['img_Profile']) ? htmlspecialchars($_SESSION['img_Profile']) : 'default-profile.jpg'; ?>" 
         alt="Profile" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 70px;">
  </div>
</div>

<!-- Sidebar -->
<div class="sidebar">
  <div class="title"><?php echo "Welcome, " . htmlspecialchars($username); ?></div>
  <a href="admin_home.php" class="active">Home</a>
  <a href="about.html">About</a>
  <a href="profile.php">My Profile</a>
  <a href="article_management.html">Manage Articles</a>
  <a href="user_management.html">Manage Users</a>
  <a href="feedback.html">Feedback</a>
  <a href="logout.php" class="logout">Logout</a>
</div>

   <div class="main-content">
        <h1><?php echo htmlspecialchars($article['title']); ?></h1>
        <div class="content">
            <div class="metadata">
                <span>By: <?php echo htmlspecialchars($article['username']); ?></span> |
                <span>Submitted on: <?php echo date("d-m-Y", strtotime($article['timePosted'])); ?></span>
            </div>
            <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
            <form method="POST" action="update_article_status.php">
                <input type="hidden" name="articleID" value="<?php echo $articleID; ?>">
                <div class="btn-group">
                    <button type="submit" name="action" value="approve" class="btn approve">Approve</button>
                    <button type="submit" name="action" value="reject" class="btn reject">Reject</button>
                </div>
            </form>
 <!-- Back Button -->
        <a href="admin_home.php" class="back-button">← Back to Admin Home</a>
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
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

  </script>
</body>
</html>
