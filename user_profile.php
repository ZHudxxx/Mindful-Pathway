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

$userID = $_SESSION['userID']; // Get userID from session

// Fetch user data
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

// Handle form submission and file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $newImgProfile = $_FILES['imgProfile'] ?? null;

  if ($newImgProfile && $newImgProfile['error'] == 0) {
    // Generate a unique name for the uploaded file to avoid overwriting
    $fileExtension = pathinfo($newImgProfile['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid("profile_", true) . "." . $fileExtension;

    // Set the target directory to store uploaded files
    $uploadDir = 'uploads/';
    $targetFile = $uploadDir . $newFileName;

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($newImgProfile['tmp_name'], $targetFile)) {
      // File upload successful, update the user's profile image in the database
      $updateQuery = "UPDATE user SET imgProfile = ? WHERE userID = ?";
      $stmt = $dbc->prepare($updateQuery);
      $stmt->bind_param("si", $targetFile, $userID);
      if ($stmt->execute()) {
        // Successfully updated profile image
        $imgProfile = $targetFile; // Update the image path
      } else {
        echo "Error updating the database: " . $stmt->error;
      }
    } else {
      echo "Error uploading file.";
    }
  }

  // Update user info (email and bio) if necessary
  $newEmail = $_POST['email'];
  $newBio = $_POST['bio'];

  $updateInfoQuery = "UPDATE user SET email = ?, bio = ? WHERE userID = ?";
  $stmt = $dbc->prepare($updateInfoQuery);
  $stmt->bind_param("ssi", $newEmail, $newBio, $userID);
  $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile | Mindful Pathway </title>
  <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="style.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background-color: #f5f5f5;

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

    /* Main Content */
    .main-content {
      flex-grow: 1;
      background-color: #fff;
      padding: 30px;
      display: flex;
      justify-content: center;
      /* Center content horizontally */
      align-items: center;
      /* Center content vertically */
      height: 100%;
    }

    h1 {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .profile-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
    }

    .profile-img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      overflow: hidden;
    }

    .profile-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .change-btn {
      background-color: #5ce1e6;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 15px;
      cursor: pointer;
      font-size: 12px;
    }

    /* Info fields */
    .info {
      width: 100%;
      max-width: 600px;
      /* Membesarkan lebar keseluruhan */
      margin-top: 20px;
    }

    .info label {
      display: block;
      margin-bottom: 5px;
      color: #666;
    }

    .info input,
    .info textarea {
      width: 100%;
      padding: 15px;
      /* Menambah padding untuk kotak lebih besar */
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    /* Memastikan kotak username, password, dan bio menjadi lebih besar dan responsive */
    .info input[type="text"],
    .info input[type="password"],
    .info textarea {
      width: 100%;
      /* Lebar 100% */
      padding: 15px;
      /* Menambah ruang di dalam kotak */
      margin-bottom: 20px;
      /* Menambah jarak antara kotak */
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    /* Menyusun elemen dalam layout flexbox untuk ruang yang lebih baik */
    .profile-container {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      /* Menyusun ke kiri */
      gap: 20px;
    }

    /* Profile Image */
    .profile-img {
      width: 150px;
      /* Membesarkan gambar profil */
      height: 150px;
      border-radius: 50%;
      overflow: hidden;
    }

    .profile-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Button Styling */
    .save-btn button {
      background-color: #5ce1e6;
      border: none;
      color: white;
      padding: 12px 25px;
      /* Lebih besar dan jelas */
      border-radius: 25px;
      /* Bentuk butang lebih bulat */
      cursor: pointer;
      transition: background 0.3s;
    }

    .save-btn button:hover {
      background-color: #28b9bf;
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
      <img src="<?php echo !empty($user['imgProfile']) ? htmlspecialchars($user['imgProfile']) : 'uploads/default_profile.jpg'; ?>"
        alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 20px;">
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
    <a href="user_profile.php" class="active">My Profile</a>
    <a href="user_article.php">Article</a>
    <a href="user_feedback.php">Feedback</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>

  <div class="main-content">
    <div class="profile-container">
      <!-- <h1>MY PROFILE</h1> -->

      <form action="user_profile.php" method="POST" enctype="multipart/form-data">
        <div class="profile-img">
          <img id="profileImage" src="<?php echo htmlspecialchars($user['imgProfile'] ?? 'uploads/default_profile.jpg'); ?>" alt="Profile Image">
          <input type="hidden" name="existingImgProfile" value="<?php echo htmlspecialchars($user['imgProfile'] ?? 'uploads/default_profile.jpg'); ?>">
        </div>
        <input type="file" name="imgProfile" accept="image/*">

        <div class="info">
          <label>Username:</label>
          <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" readonly>

          <label>Email:</label>
          <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>

          <label>Bio:</label>
          <textarea name="bio"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
        </div>

        <div class="save-btn">
          <button type="submit">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
  </div>

  <footer>
    &copy; <?php echo date('Y'); ?> Mindful Pathway. All rights reserved.
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
      const notificationsDropdown = document.getElementById('notifications-dropdown');
      notificationsDropdown.style.display = notificationsDropdown.style.display === 'block' ? 'none' : 'block';
    }

    // Close dropdown when clicking outside
    document.addEventListener("click", function(event) {
      var dropdown = document.getElementById("notifications-dropdown");
      var bellIcon = document.querySelector(".fas.fa-bell");
      if (!dropdown.contains(event.target) && event.target !== bellIcon) {
        dropdown.style.display = "none";
      }
    });

    function showNotifications() {
      const notificationsDropdown = document.getElementById('notifications-dropdown');
      notificationsDropdown.style.display = notificationsDropdown.style.display === 'block' ? 'none' : 'block';
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