<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login or show an error
    exit('Access Denied');
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message_id'], $_POST['reply'])) {
    $pdo = db_connect::getInstance();
    $stmt = $pdo->prepare("UPDATE messages SET answer = :answer WHERE id = :message_id");
    $stmt->bindParam(':answer', $_POST['reply']);
    $stmt->bindParam(':message_id', $_POST['message_id']);
    $stmt->execute();

    // Redirect back to the messages page or show a success message
    header("Location: profReadMsg.php");
    exit;
}


