<?php
require 'dbconnect.php'; // Ensure your database connection is included

// Check if notificationID is provided
if (isset($_POST['notificationID'])) {
    $notificationID = $_POST['notificationID'];

    // Update the notification to mark it as read
    $query = "UPDATE notifications SET is_read = 1 WHERE notificationID = '$notificationID'";

    if (mysqli_query($dbc, $query)) {
        echo "Notification marked as read.";
    } else {
        echo "Error: " . mysqli_error($dbc);
    }
}
