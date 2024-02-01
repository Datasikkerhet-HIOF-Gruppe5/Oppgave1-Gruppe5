<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $messageId = $_POST['message_id'];
    $replyText = $_POST['reply_text'];
    $professorId = $_SESSION['user_id']; // Assuming the professor is logged in

    // Insert reply data into the database
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("INSERT INTO replies (message_id, professor_id, reply_text) VALUES (:message_id, :professor_id, :reply_text)");
    $stmt->bindParam(':message_id', $messageId);
    $stmt->bindParam(':professor_id', $professorId);
    $stmt->bindParam(':reply_text', $replyText);
    $stmt->execute();

    echo "Reply sent successfully.";
}

