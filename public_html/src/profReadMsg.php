<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show an error
    exit('Access Denied');
}

$professorId = $_SESSION['user_id'];

$pdo = Database::getInstance();
$stmt = $pdo->prepare("SELECT m.id, m.message, m.answer, GROUP_CONCAT(c.comment SEPARATOR '|') AS comments, s.subjectName AS subject_name 
                       FROM messages m 
                       JOIN subjects s ON m.subject_id = s.id 
                       LEFT JOIN comments c ON m.id = c.message_id 
                       WHERE s.professor_id = ?
                       GROUP BY m.id");
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
            <a href="../resources/changePassword.php">Bytt passord</a>
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

    if (!empty($message['comments'])) {
        $comments = explode('|', $message['comments']);
        foreach ($comments as $comment) {
            echo "<p><strong>Anonymous comment:</strong> " . htmlspecialchars($comment) . "</p>";
        }
    }

    echo "</div><br>";
}

echo '</div>
</body>
</html>';
?>
