<?php
session_start();

$conn = new mysqli("localhost", "root", "", "mindfulpathway");
if ($conn->connect_errno) {
    echo "Failed to Open Database: " . $conn->connect_error;
    exit();
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
  <title>Mindful Pathway | Admin Dashboard</title>
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
  margin-bottom:0; 
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
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #3ea3a4;
        color: white;
    }

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
    <img src="uploads/<?php echo isset($_SESSION['img_Profile']) ? htmlspecialchars($_SESSION['img_Profile']) : 'default_profile.jpg'; ?>" 
         alt="Profile" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 70px;">
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
  <a href="admin_home.php" class="active">Home</a>
  <a href="admin_about.php">About</a>
  <a href="admin_profile.php">My Profile</a>
  <a href="article_manage.php">Manage Articles</a>
  <a href="user_manage.php">Manage Users</a>
  <a href="feedback.html">Feedback</a>
  <a href="logout.php" class="logout">Logout</a>
</div>

<!-- Main Content Area -->
<div class="main-content">
  <h1>Admin Dashboard</h1>
  <i>"The best way to predict the future is to create it." — Peter Drucker</i>

  <!-- Manage Articles Section -->
  <div class="admin-section">
    <div class="admin-card">
      <h2>Manage Articles</h2>
      <p>Here you can manage articles posted by users. You can approve or reject articles.</p>
      <table class="table table-bordered">
    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Submitted Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $article): ?>
            <tr>
                <td>
                    <a href="review_article.php?articleID=<?php echo htmlspecialchars($article['articleID']); ?>" 
                       class="article-link">
                        <?php echo htmlspecialchars($article['title']); ?>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($article['username']); ?></td>
                <td><?php echo date("d-m-Y", strtotime($article['timePosted'])); ?></td>
                <td><?php echo htmlspecialchars($article['status']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    </div>
  </div>

  <!-- Manage Users Section -->
  <div class="admin-section">
    <div class="admin-card">
      <h2>Manage Users</h2>
      <p>Preview Users of Mindful Pathway.</p>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>UserID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Bio</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo htmlspecialchars($user['userID']); ?></td>
              <td><?php echo htmlspecialchars($user['username']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo htmlspecialchars($user['bio']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

       <!-- Footer -->
  <footer>
    &copy; 2024 Mindful Pathway | All Rights Reserved
  </footer>
  <button class="back-to-top" onclick="scrollToTop()">↑</button>

  <script>
    function showNotifications() {
      alert("You have no new notifications."); 
    }
    window.onscroll = function() {
        const backToTopButton = document.querySelector('.back-to-top');
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            backToTopButton.style.display = "block"; // Show the button
        } else {
            backToTopButton.style.display = "none"; // Hide the button
        }
    };
    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
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
</body>
</html>
