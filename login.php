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

if (isset($_POST["register"])) {
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm-password"];

 
    if ($password !== $confirmPassword) {
        die("Passwords do not match.");
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);


    $sql = "INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $passwordHash, $email);

    if ($stmt->execute()) {
        echo "Account created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
