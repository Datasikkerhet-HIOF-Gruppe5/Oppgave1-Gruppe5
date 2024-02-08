<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show an error
    exit('Access Denied');
}

$professorId = $_SESSION['user_id'];

$pdo = Database::getInstance();
$stmt = $pdo->prepare("SELECT m.id, m.message, m.answer, m.anonymous_comment, s.subjectName AS subject_name 
                       FROM messages m 
                       JOIN subjects s ON m.subject_id = s.id 
                       WHERE s.professor_id = ?");
$stmt->execute([$professorId]);

$messages = $stmt->fetchAll();

// Start of HTML content
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatroom</title>
</head>
<body>
    <nav>
        <ul>
            <a href="../resources/changePassword.html">Bytt passord</a>
        </ul>
    </nav>
    <div id="messageArea" style="height: 300px; border: 1px solid #ccc; overflow-y: scroll;">';

foreach ($messages as $message) {
    echo "<div class='message'>";
    echo "<p><strong>Subject:</strong> " . htmlspecialchars($message['subject_name']) . "</p>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($message['message']) . "</p>";

    if (!empty($message['answer'])) {
        echo "<p><strong>Professor reply:</strong> " . htmlspecialchars($message['answer']) . "</p>";
    }

    if (!empty($message['anonymous_comment'])) {
        echo "<p><strong>Anonymous comment:</strong> " . htmlspecialchars($message['anonymous_comment']) . "</p>";
        echo "<hr>";
    } else {
        echo "<form action='profReplyMsg.php' method='POST'>";
        echo "<input type='hidden' name='message_id' value='" . $message['id'] . "'>";
        echo "<input type='text' name='reply' placeholder='Reply to this message'>";
        echo "<button type='submit'>Send Reply</button>";
        echo "</form>";
    }
    echo "</div><br>";
}

echo '</div>
</body>
</html>';


