<?php
session_start();
include 'db_connection.php';

// Redirect if not logged in
if (!isset($_SESSION['adminID'])) {
    header("Location: login.php");
    exit();
}

$adminID = $_SESSION['adminID']; // Changed from userID to adminID

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
    $stmt->execute();

    echo "<script>alert('Profile updated successfully!');</script>";
}

// Fetch admin data
$query = "SELECT * FROM admin WHERE adminID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $adminID);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindful Pathway - My Profile</title>
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
     
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #3cacae;
            padding: 10px 20px;
            color: white;
        }
     
        .logo {
            font-size: 20px;
            font-weight: bold;
        }
     
        .header-right {
            display: flex;
            align-items: center;
        }
     
        .search-bar {
            width: 300px;
            margin: 0 20px;
            padding: 5px 15px;           
            border-radius: 20px;
            border: none;
            background-color: #fff;
            color: #666;
        }
     
        .bell-icon {
            width: 24px;
            height: 24px;
            margin-left: 15px;
            cursor: pointer;
        }
     
        .dashboard {
            display: flex;
            flex-grow: 1;
        }
     
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #3cacae;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 20px 10px;
        }
     
        .sidebar ul {
            list-style: none;
            flex-grow: 1;
        }
     
        .sidebar ul li {
            margin: 15px 0;
        }
     
        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
     
        .sidebar ul li a:hover {
            background-color: #5ce1e6;
            cursor: pointer;
        }
     
        .logout-btn {
            background-color: #5ce1e6;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
     
        /* Main Content */
        .main-content {
            flex-grow: 1;
            background-color: #fff;
            padding: 30px;
            display: flex;
            justify-content: center; /* Center content horizontally */
            align-items: center; /* Center content vertically */
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
            max-width: 600px; /* Membesarkan lebar keseluruhan */
            margin-top: 20px;
        }
     
        .info label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
     
        .info input, .info textarea {
            width: 100%;
            padding: 15px;  /* Menambah padding untuk kotak lebih besar */
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
     
        /* Memastikan kotak username, password, dan bio menjadi lebih besar dan responsive */
        .info input[type="text"], .info input[type="password"], .info textarea {
            width: 100%; /* Lebar 100% */
            padding: 15px;  /* Menambah ruang di dalam kotak */
            margin-bottom: 20px;  /* Menambah jarak antara kotak */
            border: 1px solid #ccc;
            border-radius: 5px;
        }
     
        /* Menyusun elemen dalam layout flexbox untuk ruang yang lebih baik */
        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;  /* Menyusun ke kiri */
            gap: 20px;
        }
     
        /* Profile Image */
        .profile-img {
            width: 150px;  /* Membesarkan gambar profil */
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
            padding: 12px 25px;  /* Lebih besar dan jelas */
            border-radius: 25px;  /* Bentuk butang lebih bulat */
            cursor: pointer;
            transition: background 0.3s;
        }
     
        .save-btn button:hover {
            background-color: #28b9bf;
        }
     
        /* Footer */
        footer {
            text-align: center;
            padding: 10px;
            background-color: #3cacae;
            color: white;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">MINDFUL PATHWAY</div>
        <div class="header-right">
            <input type="text" class="search-bar" placeholder="Search...">
            <img src="img/bell.png" alt="Notification Bell" class="bell-icon">
        </div>
    </header>

    <div class="dashboard">
        <div class="sidebar">
            <ul>
                <li><a href="admin_home.php">HOME</a></li>
                <li><a href="admin_about.php">ABOUT</a></li>
                <li><a href="admin_profile.php">MY PROFILE</a></li>
                <li><a href="admin_article.php">ARTICLE</a></li>
                <li><a href="admin_feedback.php">FEEDBACK</a></li>
            </ul>
            <form action="logout.php" method="POST">
                <button type="submit" class="logout-btn">LOG OUT</button>
            </form>
        </div>

        <div class="main-content">
            <div class="profile-container">
                <h1>MY PROFILE</h1>
                <form action="admin_profile.php" method="POST" enctype="multipart/form-data">
                    <div class="profile-img">
                        <img id="profileImage" src="<?php echo htmlspecialchars($admin['imgProfile'] ?? 'uploads/default-profile.png'); ?>" alt="Profile Image">
                        <input type="hidden" name="existingImgProfile" value="<?php echo htmlspecialchars($admin['imgProfile'] ?? 'uploads/default-profile.png'); ?>">
                    </div>
                    <input type="file" name="imgProfile" accept="image/*">

                    <div class="info">
                        <label>Username:</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" readonly>

                        <label>Email:</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>

                        <label>Bio:</label>
                        <textarea name="bio" required><?php echo htmlspecialchars($admin['bio']); ?></textarea>
                    </div>

                    <div class="save-btn">
                        <button type="submit">Save Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        &copy; 2024 Mindful Pathway | All Rights Reserved
    </footer>
</body>
</html>
