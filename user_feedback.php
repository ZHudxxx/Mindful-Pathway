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
    <a href="user_home.php">Home</a>
    <a href="user_about.php">About</a>
    <a href="user_profile.php">My Profile</a>
    <a href="user_article.php">Article</a>
    <a href="user_feedback.php" class="active">Feedback</a>
    <a href="logout.php" class="logout">Logout</a>
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
(function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="Bim8_kBed-XDQ_TodjahJ";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
</script>
</body>
</html>
