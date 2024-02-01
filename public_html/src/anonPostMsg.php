<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $messageId = $_POST['message_id'];
    $commentText = $_POST['comment_text'];

    // Insert comment data into the database
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("INSERT INTO comments (message_id, comment_text) VALUES (:message_id, :comment_text)");
    $stmt->bindParam(':message_id', $messageId);
    $stmt->bindParam(':comment_text', $commentText);
    $stmt->execute();

    echo "Comment posted successfully.";
}
?>
