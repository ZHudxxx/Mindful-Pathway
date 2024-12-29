<?php
session_start();

$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "mindfulpathway";

// Establish database connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check in admins table
    $sql = "SELECT adminID, password_hash FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['adminID'] = $admin['adminID'];
            echo "<script>alert('Welcome, Admin! Redirecting to admin homepage.'); window.location.href='admin/admin_home.php';</script>";
            exit();
        }
    }

    // Check in users table
    $sql = "SELECT userID, password_hash FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['userID'] = $user['userID'];
            echo "<script>alert('Login successful! Redirecting to user homepage.'); window.location.href='user/user_home.php';</script>";
            exit();
        }
    }

    echo "<script>alert('Invalid username or password!'); window.location.href='login.html';</script>";
}

$conn->close();
?>