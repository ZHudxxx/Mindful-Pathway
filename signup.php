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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate passwords
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='signup.html';</script>";
        exit();
    }

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Check if username or email already exists
    $checkQuery = "SELECT * FROM user WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or email already exists!'); window.location.href='signup.html';</script>";
        exit();
    }

    // Insert new user
    $sql = "INSERT INTO user (username, password_hash, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $passwordHash, $email);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully! Redirecting to login page.'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error creating account: " . $stmt->error . "'); window.location.href='signup.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
