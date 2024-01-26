<?php
include 'db_connect.php';
session_start();

$studentId = $_SESSION['user_id']; // Assuming the student is logged in

// Query to fetch messages sent by the student
$stmt = $pdo->prepare("SELECT m.id, m.message_text, s.name AS subject_name FROM messages m 
                       JOIN subjects s ON m.subject_id = s.id 
                       WHERE m.student_id = ?");
$stmt->execute([$studentId]);

$messages = $stmt->fetchAll();

foreach ($messages as $message) {
    echo "Subject: " . $message['subject_name'] . "<br>";
    echo "Message: " . $message['message_text'] . "<br>";

    // Query to fetch replies for each message
    $replyStmt = $pdo->prepare("SELECT r.reply_text, p.name AS professor_name FROM replies r
                                JOIN professors p ON r.professor_id = p.id
                                WHERE r.message_id = ?");
    $replyStmt->execute([$message['id']]);
    $replies = $replyStmt->fetchAll();

    foreach ($replies as $reply) {
        echo "Reply from " . $reply['professor_name'] . ": " . $reply['reply_text'] . "<br>";
    }

    echo "<br>";
}
?>
