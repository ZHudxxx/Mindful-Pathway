<?php
// Start session
session_start();

// Database connection
$dbc = new mysqli("localhost", "root", "", "mindfulpathway");
if ($dbc->connect_errno) {
    die("Failed to connect to the database: " . $dbc->connect_error);
}

// Authentication Check
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

$userID = $_SESSION['userID'];

// Fetch user data
$query = "SELECT * FROM user WHERE userID = ?";
$stmt = $dbc->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$username = $user['username'] ?? 'Guest'; // Default to 'Guest' if username is null

// Feedback submission logic
$feedback_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['content'])) {
    // Sanitize and assign the content
    $content = $dbc->real_escape_string($_POST['content']);

    // Insert feedback into the database
    $stmt = $dbc->prepare("INSERT INTO feedback (userID, content, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("is", $userID, $content);

    if ($stmt->execute()) {
        $feedback_message = "Feedback submitted successfully!";
    } else {
        $feedback_message = "Error submitting feedback: " . $stmt->error;
    }
}

// Query to get feedback based on userID
$sql = "SELECT content, status, response_content, review_date FROM feedback WHERE userID = ?";
$stmt = $dbc->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$feedback_results = $stmt->get_result();
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
    .feedback-box {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .status {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .response {
            margin-top: 10px;
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
    <a href="user_home.php" >Home</a>
    <a href="user_about.php">About</a>
    <a href="user_profile.php">My Profile</a>
    <a href="user_article.php">Article</a>
    <a href="user_feedback.php" class="active">Feedback</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <h1>Feedback</h1>
        <?php if (!empty($feedback_message)): ?>
            <p><?= htmlspecialchars($feedback_message); ?></p>
        <?php endif; ?>

        <div class="feedback-container">
            <!-- Feedback Form -->
            <div class="feedback-form">
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <textarea name="content" placeholder="Write your feedback here..." required></textarea>
                    <button type="submit" class="submit-btn">Submit</button>
                </form>
            </div>

            <!-- Display Feedback -->
            <?php while ($feedback = $feedback_results->fetch_assoc()): ?>
                <div class="feedback-card">
                    <h3>Status: <?= htmlspecialchars($feedback['status']); ?></h3>
                    <p>Feedback: <?= htmlspecialchars($feedback['content']); ?></p>
                    <?php if (!empty($feedback['response_content'])): ?>
                        <p>Response: <?= htmlspecialchars($feedback['response_content']); ?></p>
                        <p>Reviewed on: <?= htmlspecialchars($feedback['review_date']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
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
(function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="Bim8_kBed-XDQ_TodjahJ";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
</script>
</body>
</html>
