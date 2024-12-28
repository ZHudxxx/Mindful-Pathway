<?php
session_start();

// Directly establish the database connection here
$dbc = mysqli_connect("localhost", "root", "", "mindfulpathway");
if (mysqli_connect_errno()) {
    echo "Failed to Open Database: " . mysqli_connect_error();
    exit();
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $query = "SELECT * FROM admin WHERE username = '$username'";
    $result = mysqli_query($dbc, $query);

    if (mysqli_num_rows($result) == 0) {
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}

if (isset($_POST['approve']) || isset($_POST['reject'])) {
    $article_id = $_POST['articleID'];
    $status = isset($_POST['approve']) ? 'approved' : 'rejected';

    $update_query = "UPDATE article SET status = '$status' WHERE articleID = '$articleID'";
    mysqli_query($dbc, $update_query);
}

$query = "SELECT * FROM article WHERE status IS NULL ORDER BY timePosted DESC LIMIT 3";
$result = mysqli_query($dbc, $query);

$articles = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Set default status to 'Pending' if no action has been taken yet
    if (!$row['status']) {
        $row['status'] = 'Pending';
    }
    $articles[] = $row;
}

$query_users = "SELECT userID, username, bio, email FROM user ORDER BY username ASC LIMIT 5";
$result_users = mysqli_query($dbc, $query_users);

$users = [];
while ($row = mysqli_fetch_assoc($result_users)) {
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
      <img src="uploads/<?php echo isset($_SESSION['img_Profile']) ? $_SESSION['img_Profile'] : 'default-profile.jpg'; ?>" 
           alt="Profile" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 70px;">
    </div>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="title"><?php echo "Welcome, $username"; ?></div>
    <a href="admin_home.php" class="active">Home</a>
    <a href="about.html">About</a>
    <a href="profile.php">My Profile</a>
    <a href="article_management.html">Manage Articles</a>
    <a href="user_management.html">Manage Users</a>
    <a href="feedback.html">Feedback</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>

  <!-- Main Content Area -->
  <div class="main-content">
    <h1>Admin Dashboard</h1>
    <i>"The best way to predict the future is to create it." — Peter Drucker</i>

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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($article['title']); ?></td>
                            <td><?php echo htmlspecialchars($article['authorID']); ?></td>
                            <td><?php echo htmlspecialchars($article['timePosted']); ?></td>
                            <td><?php echo $article['status']; ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="article_id" value="<?php echo $article['articleID']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success">Approve</button>
                                    <button type="submit" name="reject" class="btn btn-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Manage Users Section (Preview) -->
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
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Function to handle article approval
    function approveArticle(articleID) {
      if (confirm("Are you sure you want to approve this article?")) {
        alert("Article " + articleID + " approved!");
      }
    }
  </script>
</body>
</html>
