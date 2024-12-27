<?php
session_start();


$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "mindfulpathway";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $_POST["username"];
    $inputPassword = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
      
        if (password_verify($inputPassword, $user['password_hash'])) {
           
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['username'] = $user['username'];
            echo "Login successful. Welcome, " . $user['username'] . "!";
        } else {
            echo "Invalid password. Please try again.";
        }
    } else {
        echo "User not found. Please check your username.";
    }
    $stmt->close();
}


if (isset($_POST["register"])) {
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $bio = $_POST["bio"] ?? "";
    $imgProfile = $_POST["imgProfile"] ?? "";

    $sql = "INSERT INTO users (username, password_hash, email, bio, imgProfile) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $passwordHash, $email, $bio, $imgProfile);

    if ($stmt->execute()) {
        echo "Account created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
