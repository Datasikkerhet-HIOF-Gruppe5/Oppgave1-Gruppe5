<?php
include 'db_connect.php';
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in as anonymous
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'anonymous') {
    exit('Access Denied');
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message_id'], $_POST['anonymous_comment'])) {
    $messageId = $_POST['message_id'];
    $anonymousComment = $_POST['anonymous_comment'];

    $pdo = db_connect::getInstance();
    // Insert the anonymous comment into the database
    $stmt = $pdo->prepare("UPDATE messages SET anonymous_comment = :comment WHERE id = :message_id");
    $stmt->bindParam(':comment', $anonymousComment);
    $stmt->bindParam(':message_id', $messageId);
    $stmt->execute();

    // Redirect back to the messages page
    header("Location: anonReadMsg.php");
    exit;
}
