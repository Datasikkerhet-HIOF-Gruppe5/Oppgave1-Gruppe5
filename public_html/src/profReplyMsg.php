<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $messageId = $_POST['message_id'];
    $replyText = $_POST['reply_text'];
    $professorId = $_SESSION['user_id']; // Assuming the professor is logged in

    $stmt = $pdo->prepare("INSERT INTO replies (message_id, professor_id, reply_text) VALUES (?, ?, ?)");
    $stmt->execute([$messageId, $professorId, $replyText]);

    echo "Reply sent successfully.";
}
?>
