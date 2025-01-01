<?php
session_start();

// Database connection
$dbc = new mysqli("localhost", "root", "", "mindfulpathway");
if ($dbc->connect_errno) {
    echo "Failed to Open Database: " . $dbc->connect_error;
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
if (isset($_GET['userID'])) {
  $userID = $_GET['userID'];
  
  // Fetch user details
  $query = "SELECT * FROM user WHERE userID = '$userID'";
  $result = mysqli_query($dbc, $query);

  if (!$result) {
      die('Query Failed: ' . mysqli_error($dbc));
  }

  $user = mysqli_fetch_assoc($result);
  if (!$user) {
      die('User not found.');
  }
} else {
  die('User ID not provided.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
  $username = mysqli_real_escape_string($dbc, $_POST['username']);
  $email = mysqli_real_escape_string($dbc, $_POST['email']);
  $bio = mysqli_real_escape_string($dbc, $_POST['bio']);
  
  if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
      $imageTmpPath = $_FILES['image']['tmp_name'];
      $imageName = $_FILES['image']['name'];
      $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
      $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

      if (in_array(strtolower($imageExt), $allowedExt)) {
          $newImageName = 'user_' . $userID . '.' . $imageExt;
          $imageDest = 'uploads/' . $newImageName;

          if (move_uploaded_file($imageTmpPath, $imageDest)) {
              $imagePath = $imageDest;
          } else {
              echo "Error uploading the image.";
              $imagePath = $user['imgProfile'];  // Keep current image if upload fails
          }
      } else {
          echo "Invalid image format. Only JPG, PNG, and GIF are allowed.";
          $imagePath = $user['imgProfile'];  // Keep current image if format is not valid
      }
  } else {
      $imagePath = $user['imgProfile'];  // Keep current image if no new image is uploaded
  }

  // Update query
  $updateQuery = "UPDATE user SET username = '$username', email = '$email', bio = '$bio', imgProfile = '$imagePath' WHERE userID = '$userID'";
  $updateResult = mysqli_query($dbc, $updateQuery);
  
  if ($updateResult) {
      header("Location: admin_user_manage.php"); 
      exit();
  } else {
      echo "Error updating user: " . mysqli_error($dbc);
  }
}

if (isset($_POST['delete_user'])) {
  // Delete the user from the database
  $deleteQuery = "DELETE FROM user WHERE userID = '$userID'";
  $deleteResult = mysqli_query($dbc, $deleteQuery);

  if ($deleteResult) {
      header('Location: admin_user_manage.php');
      exit();
  } else {
      die('Error deleting user: ' . mysqli_error($dbc));
  }
}
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
        margin-bottom: 30px;
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

/* Form Styles */
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    padding: 20px;
    margin-left:20px ;
}

label {
    font-weight: bold;
    color: #555;
}

input[type="text"], input[type="email"], textarea {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    width: 100%;
}

input[type="file"] {
    padding: 5px;
    font-size: 16px;
}

textarea {
    min-height: 100px;
    resize: vertical;
}
input[readonly].grey-input {
    background-color:#c2dfe0; 
    color:rgb(0, 0, 0); 
    border: 1px solidrgb(0, 0, 0);
    cursor: not-allowed; 
}
textarea[readonly].grey-input {
    background-color:#c2dfe0; 
    color: #666666;
    border: 1px solid #ccc; 
    cursor: not-allowed; 
    resize: none; 
}
.buttons-container {
    display: flex;
    gap: 10px; 
}

.update-button {
    background-color: #4CAF50; 
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

.update-button:hover {
    background-color: #45a049; 
}

.delete-button {
    background-color: #f44336; 
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

.delete-button:hover {
    background-color: #e53935; 
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
    <div class="title"><?php echo "Welcome, $username"; ?></div>
    <a href="admin_home.php" >Home</a>
    <a href="admin_about.php">About</a>
    <a href="admin_profile.php">My Profile</a>
    <a href="article_manage.php">Manage Articles</a>
    <a href="admin_user_manage.php"  class="active">Manage Users</a>
    <a href="feedback.html">Feedback</a>

    <a href="logout.php" class="logout">Logout</a>
  </div>

 <!-- Main Content Area -->
<div class="main-content">
    <h1>User Details</h1>

    <div class="container">
        <div class="admin-section">
            <div class="admin-card">
                <form method="POST" action="" enctype="multipart/form-data">
                    
                    <!-- Show Profile Image -->
                    <label for="image">Profile Image:</label>
                    <?php if (!empty($user['imgProfile']) && file_exists('uploads/' . $user['imgProfile'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($user['imgProfile']); ?>" alt="Profile Picture" style="width: 150px; height: 150px;">
                    <?php else: ?>
                        <img src="uploads/default_profile.jpg" alt="Default Profile Picture" style="width: 150px; height: 150px;">
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*">
                    
                    <label for="userId">User ID:</label>
                    <input type="text" name="userId" value="<?php echo htmlspecialchars($user['userID']); ?>" readonly class="grey-input">

                    <label for="username">Username:</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly class="grey-input">

                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                    <label for="bio">Bio:</label>
                    <textarea name="bio" readonly class="grey-input"><?php echo htmlspecialchars($user['bio']); ?></textarea>


                    <div class="buttons-container">
                        <button type="submit" name="update_user" class="update-button">Update User</button>
                        <button type="submit" name="delete_user" class="delete-button" onclick="return confirm('Are you sure you want to delete this user?')">Delete User</button>
                    </div>
                </form>
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
            const usernameCell = row.getElementsByTagName('td')[1]; 
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
    function confirmDelete(userID) {
        var confirmation = confirm("Are you sure you want to delete this user?");
        if (confirmation) {
            window.location.href = "delete_user.php?userID=" + userID;
        }
    }
  </script>
</body>
</html>
