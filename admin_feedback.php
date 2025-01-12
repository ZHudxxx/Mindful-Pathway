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

// Fetch all feedback from the database
$query = "SELECT * FROM feedback";
$result = mysqli_query($conn, $query);

// Handle feedback updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedbackID = $_POST['feedbackID'];
    $status = $_POST['status'];
    $response_content = $_POST['response_content'];
    $review_date = date("Y-m-d H:i:s"); // Current date

    $updateQuery = "UPDATE feedback 
                    SET status = ?, response_content = ?, review_date = ?, reviewed = 1 
                    WHERE feedbackID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $status, $response_content, $review_date, $feedbackID);

    if ($stmt->execute()) {
        $message = "Feedback updated successfully.";
    } else {
        $message = "Error updating feedback.";
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
            margin: 0;
            font-family: 'Roboto', Arial, sans-serif;
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

        .header .menu img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
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
            display: block;
            transition: 0.3s;
        }

        .sidebar a {
            padding: 15px 20px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .sidebar .title {
            font-size: 24px;
            padding-left: 20px;
            margin-bottom: 30px;
        }

        .sidebar .active {
            background-color: #5ce1e6;
        }

        .sidebar .logout {
            background-color: #5ce1e6;
            color: #333;
            text-align: center;
            padding: 10px 20px;
            border-radius: 25px;
            margin: 20px auto 0;
            display: block;
            width: 80%;
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
            margin-bottom: 10px;
            color: #333;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
        }

        .banner {
            width: 100%;
            height: 300px;
            background-image: url('img/banner1.png');
            background-size: cover;
            background-position: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #3cacae;
            color: white;
        }

        form {
            display: inline-block;
        }

        form select, form textarea, form button {
            margin-top: 5px;
            font-size: 14px;
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
        }

        .back-to-top {
            display: none;
            position: fixed;
            bottom: 70px;
            right: 20px;
            background-color: #3cacae;
            color: white;
            padding: 10px;
            border-radius: 50%;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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
    </style>
</head>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css"> <!-- Assuming you have a CSS file for styles -->
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
    <img src="<?php echo !empty($admin['imgProfile']) ? htmlspecialchars($admin['imgProfile']) : 'uploads/default-profile.png'; ?>"
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
  <a href="admin_home.php" >Home</a>
  <a href="admin_about.php">About</a>
  <a href="admin_profile.php">My Profile</a>
  <a href="article_manage.php">Manage Articles</a>
  <a href="admin_user_manage.php">Manage Users</a>
  <a href="admin_feedback.php" class="active">Feedback</a>
  <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">
        <h1>User Feedback Management (Admin)</h1>
        <?php if (!empty($message)) { ?>
            <p style="color: green;"><?php echo $message; ?></p>
        <?php } ?>

<table>
    <thead>
        <tr>
            <th>Feedback ID</th>
            <th>User</th>
            <th>Content</th>
            <th>Status</th>
            <th>Response</th>
            <th>Review Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['feedbackID']; ?></td>
                <td><?php echo $row['userID']; ?></td>
                <td><?php echo $row['content']; ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td><?php echo $row['response_content']; ?></td>
                <td><?php echo $row['review_date']; ?></td>
                <td>
                    <form method="POST" action="admin_feedback.php">
                        <input type="hidden" name="feedbackID" value="<?php echo $row['feedbackID']; ?>">
                        <label for="status">Status:</label>
                        <select name="status">
                            <option value="pending" <?php if ($row['status'] === 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="ignore" <?php if ($row['status'] === 'ignore') echo 'selected'; ?>>Ignore</option>
                            <option value="replied" <?php if ($row['status'] === 'replied') echo 'selected'; ?>>Replied</option>
                        </select>
                        <br>
                        <label for="response_content">Response:</label>
                        <textarea name="response_content" rows="2" cols="30"><?php echo $row['response_content']; ?></textarea>
                        <br>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

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

