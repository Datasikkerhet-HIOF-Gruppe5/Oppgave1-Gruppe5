<?php
include 'db_connect.php';
session_start();

header("Content-Security-Policy: upgrade-insecure-requests");

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'anonymous') {
    exit('Access Denied');
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message_id'], $_POST['report_text'])) {
    $messageId = $_POST['message_id'];
    $reportText = $_POST['report_text'];

    $pdo = Database::getInstance();
    // Insert the anonymous report into the database
    $stmt = $pdo->prepare("INSERT INTO message_reports (message_id, report_text) VALUES (:message_id, :report_text)");
    $stmt->bindParam(':message_id', $messageId);
    $stmt->bindParam(':report_text', $reportText);
    $stmt->execute();

    // Redirect back to the messages page after 3 seconds
    header("Refresh:3; url=anonReadMsg.php");
    echo "Report submitted successfully. Redirecting back in 3 seconds...";
} else {
    // Handle error or redirect if not a POST request
    // Redirect immediately if the form wasn't submitted correctly
    header("Location: anonReadMsg.php");
    exit;
}