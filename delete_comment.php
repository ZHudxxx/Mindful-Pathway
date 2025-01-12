<?php
require_once __DIR__ . '/DBConnect.php'; // Ensure correct path resolution
$conn = mysqli_connect("localhost",
    "root",
    "",
    "mindfulpathway"
);
if (isset($_GET['comment_id'])) {
    $commentID = intval($_GET['comment_id']); // Sanitize the input

    // Fetch the articleID associated with the comment
    $query = "SELECT articleID FROM comment WHERE commentID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $commentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $articleID = $row['articleID'];
    } else {
        die("Article ID could not be determined.");
    }

    // Recursive function to delete child comments
    function deleteComments($dbc, $commentID)
    {
        // Find child comments
        $query = "SELECT commentID FROM comment WHERE parentID = ?";
        $stmt = $dbc->prepare($query);
        $stmt->bind_param("i", $commentID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            deleteComments($dbc, $row['commentID']); // Recursive call for children
        }

        // Delete the current comment
        $deleteQuery = "DELETE FROM comment WHERE commentID = ?";
        $deleteStmt = $dbc->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $commentID);
        $deleteStmt->execute();
    }

    // Call the recursive function to delete the comment and its replies
    deleteComments($conn, $commentID);

    // Redirect back to the article page
    header("Location: handle_comment.php?articleID=" . htmlspecialchars($articleID, ENT_QUOTES, 'UTF-8'));
    exit;
} else {
    echo "Comment ID is not set.";
}

