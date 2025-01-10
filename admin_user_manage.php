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

// Fetch the logged-in admin details
$query = "SELECT * FROM admin WHERE adminID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $adminID);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Fetch all users
$query = "SELECT * FROM user";
$result = $conn->query($query);

// Check for errors in query
if (!$result) {
    die('Query Failed: ' . $conn->error);
}

$users = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management | Mindful Pathway </title>
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
        margin-top: 10px;
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
        border-radius: 25px;
        cursor: pointer;
    }

    .admin-btn:hover {
        background-color: #359799;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        margin-bottom: 20px;
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
    .container {
    margin-bottom: 90px;
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
.search-bar {
            margin-bottom: 20px;
            align-items: center;
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
button {
  padding: 12px 20px;
  background-color: #3ea3a4; 
  color: white;
  border: none;
  border-radius: 25px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
  margin-top: 20px;
}

button:hover {
  background-color: #359799; 
  transform: scale(1.05);
}

button:active {
  background-color: #298c88; 
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
  <a href="admin_user_manage.php" class="active">Manage Users</a>
  <a href="admin_feedback.php">Feedback</a>
  <a href="logout.php" class="logout">Logout</a>
</div>

 <!-- Main Content Area -->
 <div class="main-content">
    <h1>Manage Users</h1>
    <i>Manage user details.</i>

    <div class="container">
    <button onclick="window.location.href='admin_add_user.php'" style="margin-left: 1000px;">ADD NEW USERS</button>

        
<!-- Manage Users Section -->
  <div class="admin-section">
    <div class="admin-card">
    <div class="search-bar">
      <input type="text" id="searchInput" class="form-control" placeholder="Search users...">
      </div>
      <table class="table table-bordered" id="userTable">
        <thead>
          <tr>
            <th>UserID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Bio</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo htmlspecialchars($user['userID']); ?></td>
              <td><?php echo htmlspecialchars($user['username']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo htmlspecialchars($user['bio']); ?></td>
              <td><button class="admin-btn" onclick="window.location.href='admin_user_details.php?userID=<?php echo $user['userID']; ?>'">View Details</button></td>
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

    function scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    const searchInput = document.getElementById('searchInput');
    const userTable = document.getElementById('userTable');

    searchInput.addEventListener('keyup', function () {
        const filter = searchInput.value.toLowerCase();
        const rows = userTable.getElementsByTagName('tr');
        Array.from(rows).forEach(row => {
            const usernameCell = row.getElementsByTagName('td')[1]; // Assuming username is in the second column
            if (usernameCell) {
                const usernameText = usernameCell.textContent || usernameCell.innerText;
                row.style.display = usernameText.toLowerCase().includes(filter) ? '' : 'none';
            }
        });
    });

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
