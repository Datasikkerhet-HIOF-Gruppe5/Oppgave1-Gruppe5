<?php
include 'db_connect.php';
session_start();

$professorId = $_SESSION['user_id']; // Assuming the professor is logged in

$pdo = Database::getInstance();
$stmt = $pdo->prepare("SELECT m.id, m.message_text, s.name AS subject_name FROM messages m 
                       JOIN subjects s ON m.subject_id = s.id 
                       WHERE s.professor_id = ?");
$stmt->execute([$professorId]);

$messages = $stmt->fetchAll();
foreach ($messages as $message) {
    echo "Subject: " . $message['subject_name'] . "<br>";
    echo "Message: " . $message['message_text'] . "<br><br>";
}
?>
