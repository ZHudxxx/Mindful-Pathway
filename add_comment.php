<?php
require 'dbconnect.php'; // Connect to the database
session_start(); // Start the session to access user data

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    echo "<script>
        alert('You must be logged in to post a comment.');
        window.location.href = 'login.html'; // Replace with your login page URL
    </script>";
    exit; // Ensure the script stops executing after the redirect
}


// Get the user ID from the session
$userID = $_SESSION['userID'];

// Get the form data
$articleID = $_POST['article_id'];
$commentContent = trim($_POST['comment_content']);

// Validate the input
if (empty($commentContent)) {
    die("Comment cannot be empty.");
}

// Prepare and execute the query to insert the comment
$commentContent = mysqli_real_escape_string($dbc, $commentContent);
$query = "INSERT INTO comment (content, timePosted, userID, articleID) 
          VALUES ('$commentContent', NOW(),'$userID','$articleID')";

if (mysqli_query($dbc, $query)) {
    header("Location: readarticle.php?id=$articleID"); // Redirect back to the article page
    exit;
} else {
    echo "Error: " . mysqli_error($dbc);
}
