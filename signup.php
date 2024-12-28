<?php
session_start();

$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "mindfulpathway";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='signup.html';</script>";
        exit();
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $isCompanyEmail = strpos($email, '@mindfulpathway.com') !== false;

    $checkQuery = $isCompanyEmail 
        ? "SELECT * FROM admin WHERE username = ? OR email = ?" 
        : "SELECT * FROM user WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or email already exists!'); window.location.href='signup.html';</script>";
        exit();
    }

    if ($isCompanyEmail) {
        $insertQuery = "INSERT INTO admin (username, password_hash, email) VALUES (?, ?, ?)";
    } else {
        $insertQuery = "INSERT INTO user (username, password_hash, email) VALUES (?, ?, ?)";
    }
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $username, $passwordHash, $email);

    if ($stmt->execute()) {
        $redirectPage = $isCompanyEmail ? 'admin_dashboard.html' : 'login.html';
        echo "<script>alert('Account created successfully! Redirecting to login page.'); window.location.href='$redirectPage';</script>";
    } else {
        echo "<script>alert('Error creating account: " . $stmt->error . "'); window.location.href='signup.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
