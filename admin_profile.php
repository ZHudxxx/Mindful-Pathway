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
if (!isset($_SESSION['adminID'])) {
    header('Location: login.php');
    exit();
}

$adminID = $_SESSION['adminID']; // Get adminID from session

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
    $stmt = $dbc->prepare($query);
    $stmt->bind_param("sssi", $email, $bio, $uploadedImage, $adminID);
    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!');</script>";
    } else {
        echo "<script>alert('Failed to update profile.');</script>";
    }
}

// Fetch admin data
$query = "SELECT * FROM admin WHERE adminID = ?";
$stmt = $dbc->prepare($query);
$stmt->bind_param("i", $adminID);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Mindful Pathway</title>
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

        @media (max-width: 768px) {
            .profile-img {
                width: 100px;
                /* Smaller profile image for mobile */
                height: 100px;
            }
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
    <div class="sidebar">
        <div class="title"><?php echo "Welcome, " . htmlspecialchars($admin['username']); ?></div>
        <a href="admin_home.php">Home</a>
        <a href="admin_about.php">About</a>
        <a href="admin_profile.php" class="active">My Profile</a>
        <a href="article_manage.php">Manage Articles</a>
        <a href="admin_user_manage.php">Manage Users</a>
        <a href="admin_feedback.php">Feedback</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="main-content">
        <div class="profile-container">
            <!---   <h1>MY PROFILE</h1> -->
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
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
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