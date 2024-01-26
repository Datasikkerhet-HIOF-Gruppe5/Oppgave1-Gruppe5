<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $messageId = $_POST['message_id'];

    // Flag the message as inappropriate
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("UPDATE messages SET reported = 1 WHERE id = :message_id");
    $stmt->bindParam(':message_id', $messageId);
    $stmt->execute();

    echo "Message reported successfully.";
}
?>
