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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> About | Mindful Pathway</title>
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
      margin-bottom: 15px;
    }
    
        .benda-about {
          display: flex;
          flex-wrap: wrap;
          gap: 20px;
        }
    
        .about-card {
          width: 550px;
          background-color: white;
          border-radius: 8px;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
          overflow: hidden;
          margin-bottom: 50px;
          padding: 20px;
        }
    
        .about-card img {
          width: 50px;
          height: 50px;
          object-fit: cover;
        }
    
        .about-card .content {
          padding: 10px;
        }
    
        .about-card .content h2 {
          margin: 0;
          font-size: 26px;
          color: #333;
        }
        .about-card .content h3 {
          margin: 0;
          font-size: 22px;
          color: #333;
        }
        .about-card .content p {
          color: #555;
          font-size: 14px;
          margin-top: 4px;
        }
    .about-button {
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
    
    .about-button:hover {
      background-color: #2b8c8b;
      transform: translateY(-3px);
    }
    
    .about-button:active {
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
    <a href="user_about.php"  class="active">About</a>
    <a href="user_profile.php">My Profile</a>
    <a href="user_article.php">Article</a>
    <a href="user_feedback.php">Feedback</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>

       <!-- Main Content Area -->
  <div class="main-content">
    <h1>ABOUT</h1>
    <i>About Us and our infomation</i>
    <div class="benda-about"id="benda-about">
        <div class="about-card">
            <div class="content">
    <h2>Welcome to Mindful pathway</h2>
    <p>At Mindful pathway, we aim to create a supportive and informative platform for mental health and psychology awareness.
    Whether you're here to share your thoughts, gain insights, or seek help, we’re here to empower you with knowledge and connections.</p>
</div>
</div>
    <div class="about-card">
        <div class="content">
            <h3>Our Mission</h3>
    <p> - Raise awareness about mental health and well-being.<br>
        - Foster a community where users can share and learn through articles and discussions.<br>
        - Provide tools like AI interaction to guide users toward better mental health practices.</p>
    </div>
</div>
<div class="about-card">
    <div class="content">
        <h3>Why Choose Us?</h3>
        <p>We believe that mental health is as important as physical health. At Mindful pathway, you’re not just a user—you’re part of a community that values empathy, support, and understanding.</p>
      </div>
    </div>
    <div class="about-card">
        <div class="content">
            <h3>Need Assistance?</h3>
            <p>If you have questions or need support, feel free to contact us:<br>
                Email: support@mindfulpathway.com <br>
                Live Chat: Available on our platform.<br>
                Help Center: Access FAQs and resources for quick assistance.</p>
          </div>
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
            backToTopButton.style.display = "block";
        } else {
            backToTopButton.style.display = "none"; 
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
<script>
(function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="Bim8_kBed-XDQ_TodjahJ";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
</script>
</body>
</html>
