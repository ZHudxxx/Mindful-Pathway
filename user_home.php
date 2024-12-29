<?php
session_start();

// Directly establish the database connection here
$dbc = mysqli_connect("localhost", "root", "", "mindfulpathway");
if (mysqli_connect_errno()) {
    echo "Failed to Open Database: " . mysqli_connect_error();
    exit();
}

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Assuming 'username' is stored in session
} else {
    header('Location: login.php');
    exit();
}

// Initialize articles array to avoid errors if the query returns no results
$articles = [];

// Query to fetch the latest articles
$query = "SELECT * FROM article WHERE status = 'approved' ORDER BY timePosted DESC LIMIT 3"; 
$result = mysqli_query($dbc, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($dbc));
}

// Fetch articles into array
while ($row = mysqli_fetch_assoc($result)) {
    $articles[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mindful Pathway | User Dashboard</title>
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

    .recommended-articles {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .article-card {
      width: 500px;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      margin-bottom: 50px;
    }

    .article-card img {
      width: 50px;
      height: 50px;
      object-fit: cover;
    }

    .article-card .content {
      padding: 10px;
    }

    .article-card .content h3 {
      margin: 0;
      font-size: 18px;
      color: #333;
    }

    .article-card .content p {
      color: #555;
      font-size: 14px;
    }
.article-button {
  display: inline-block;
  padding: 10px 20px;
  background-color: #3cacae;
  color: white;
  text-decoration: none;
  border-radius: 25px;
  font-weight: bold;
  text-align: center;
  transition: background-color 0.3s, transform 0.3s;
  margin-top: 10px;
}

.article-button:hover {
  background-color: #2b8c8b;
  transform: translateY(-3px);
}

.article-button:active {
  background-color: #1f6363;
  transform: translateY(1px);
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
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <div class="logo">
      <img src="img/favicon.png" alt="Logo">
      <span>Mindful Pathway</span>
    </div>
    <div class="search-bar">
   <form method="GET" action="search.php" id="search-form">
      <input type="text" name="query" placeholder="Search..." id="search-input">
      <button type="submit" class="fa fa-search"></button>
   </form>
</div>
    <div class="menu">
      <i class="fas fa-bell" style="font-size: 20px; margin-right: 20px;" onclick="showNotifications()"></i>
      <img src="uploads/<?php echo isset($_SESSION['img_Profile']) ? $_SESSION['img_Profile'] : 'default-profile.jpg'; ?>" 
           alt="Profile" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 70px;">
    </div>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="title"><?php echo "Welcome, " . htmlspecialchars($username); ?></div>
    <a href="user_home.php" class="active">Home</a>
    <a href="user_about.php">About</a>
    <a href="profile.php">My Profile</a>
    <a href="articles.php">Article</a>
    <a href="feedback.html">Feedback</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>

  <!-- Main Content Area -->
  <div class="main-content">
    <h1>HOME</h1>
    <i>"It is not that I'm so smart. But I stay with the questions much longer." — Albert Einstein</i>
    <div class="banner"></div>

      <!-- Recommended Articles Section -->
    <h2>Recommended Articles</h2>
    <div class="recommended-articles" id="recommended-articles">
      <?php if (empty($articles)): ?>
        <p>No articles found.</p>
      <?php else: ?>
        <?php foreach ($articles as $article): ?>
          <div class="article-card">
            <img src="img/<?php echo htmlspecialchars($article['coverIMG'] ?: 'default.png'); ?>" 
                 alt="<?php echo htmlspecialchars($article['title']); ?>">
            <div class="content">
              <h3><?php echo htmlspecialchars($article['title']); ?></h3>
              <p><?php echo htmlspecialchars(substr($article['content'], 0, 100)); ?>...</p>
              <a href="article.php?id=<?php echo $article['articleID']; ?>" class="article-button">Read More</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
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
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Bell notification function
    function showNotifications() {
      alert("You have no new notifications.");
    }
      document.getElementById('search-form').addEventListener('submit', function (e) {
   const query = document.getElementById('search-input').value.toLowerCase().trim();
   const articles = document.querySelectorAll('.article-card');

   // Check if query is empty
   if (!query) {
       alert('Please enter a search term.');
       e.preventDefault(); // Prevent form submission
       return;
   }

   let found = false;

   // Perform filtering
   articles.forEach(article => {
       const title = article.querySelector('h3').textContent.toLowerCase();
       const content = article.querySelector('p').textContent.toLowerCase();

       if (title.includes(query) || content.includes(query)) {
           article.style.display = 'block';
           found = true;
       } else {
           article.style.display = 'none';
       }
   });

   // If no articles are found
   if (!found) {
       alert('No matching articles found.');
   }

   // Prevent form submission to the server if searching locally
   e.preventDefault();
});
  </script>
</body>
</html>
