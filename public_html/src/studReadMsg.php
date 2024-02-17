<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    exit('Access Denied');
}

$pdo = Database::getInstance();
$subjectId = null;
$messages = [];

// Handle new message submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_message'], $_POST['subject_id'])) {
        $newMessage = $_POST['new_message'];
        $subjectId = $_POST['subject_id'];
        $student_id = $_SESSION['user_id'];
        // Insert new message into database (ensure you sanitize and validate this input)
        $stmt = $pdo->prepare("INSERT INTO messages (message, subject_id, student_id) VALUES (:message, :subject_id, :student_id)");
        $stmt->bindParam(':message', $newMessage);
        $stmt->bindParam(':subject_id', $subjectId);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
    } else if (isset($_POST['subject_id'])) {
        $subjectId = $_POST['subject_id'];
    }

    // Fetch messages for the selected subject
    if ($subjectId) {
        $stmt = $pdo->prepare("SELECT m.message, m.answer, c.comment 
                               FROM messages m 
                               LEFT JOIN comments c ON m.id = c.message_id 
                               WHERE m.subject_id = ?");
        $stmt->execute([$subjectId]);
        $messages = $stmt->fetchAll();
    }
}

// Fetch all subjects
$stmt = $pdo->prepare("SELECT id, subjectName FROM subjects");
$stmt->execute();
$subjects = $stmt->fetchAll();

// Start of HTML content
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Selection</title>
</head>
<body>
    <h2>Select a Subject</h2>
    <ul>';

foreach ($subjects as $subject) {
    echo "<li>" . htmlspecialchars($subject['subjectName']) . " 
              <form action='' method='POST'>
                  <input type='hidden' name='subject_id' value='" . $subject['id'] . "'>
                  <button type='submit'>Messages</button>
              </form>
          </li>";
}

echo '</ul>';

if ($subjectId) {
    echo "<h2>Messages for Subject ID: " . htmlspecialchars($subjectId) . "</h2>";

    foreach ($messages as $message) {
        echo "<div class='message'>";
        echo "<p>Message: " . htmlspecialchars($message['message']) . "</p>";
        if (!empty($message['answer'])) {
            echo "<p>Professor reply: " . htmlspecialchars($message['answer']) . "</p>";
        }
        if (!empty($message['comment'])) {
            echo "<p>Anonymous comment: " . htmlspecialchars($message['comment']) . "</p>";
        }
        echo "</div><br>";
    }

    // Form to submit a new message
    echo "<form action='' method='POST'>
            <input type='hidden' name='subject_id' value='" . htmlspecialchars($subjectId) . "'>
            <textarea name='new_message' placeholder='Type your message here'></textarea>
            <button type='submit'>Send New Message</button>
          </form>";
}

echo '</body>
</html>';
?>
