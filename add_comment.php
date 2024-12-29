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
$parentID = isset($_POST['parent_id']) && is_numeric($_POST['parent_id']) ? $_POST['parent_id'] : NULL; // Parent ID for replies

// Validate the input
if (empty($commentContent)) {
    die("Comment cannot be empty.");
}

// Prepare and execute the query to insert the comment
$commentContent = mysqli_real_escape_string($dbc, $commentContent);
$query = "INSERT INTO comment (content, timePosted, userID, articleID, parentID) 
          VALUES ('$commentContent', NOW(), '$userID', '$articleID', " . ($parentID ? "'$parentID'" : "NULL") . ")";

if (mysqli_query($dbc, $query)) {

    // If the comment is a reply, send a notification to the original commenter
    if ($parentID) {
        // Get the user ID of the person who posted the original comment
        $getOriginalCommentQuery = "SELECT userID FROM comment WHERE commentID = '$parentID'";
        $result = mysqli_query($dbc, $getOriginalCommentQuery);
        $originalComment = mysqli_fetch_assoc($result);
        $originalUserID = $originalComment['userID'];

        // Insert a notification for the user who posted the original comment
        $message = $originalUserID." have reply to your comment.";
        $notificationQuery = "INSERT INTO notifications (userID, commentID, articleID, messages) 
                               VALUES ('$originalUserID', '$parentID','$articleID', '$message')";
        mysqli_query($dbc, $notificationQuery);
    }

    header("Location: readarticle.php?id=$articleID"); // Redirect back to the article page
    exit;
} else {
    echo "Error: " . mysqli_error($dbc);
}
