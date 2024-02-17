<?php
include 'db_connect.php';
session_start();

// Check if the user is logged in as anonymous
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'anonymous') {
    exit('Access Denied');
}

$pdo = Database::getInstance();
$stmt = $pdo->prepare("SELECT subjects.id, subjects.subjectName, professors.pictureFile
                       FROM subjects
                       INNER JOIN professors ON subjects.professor_id = professors.id");
$stmt->execute();
$subjects = $stmt->fetchAll();

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gjest</title>
</head>
<body>
    <h2>Select a Subject</h2>
    <ul>';

foreach ($subjects as $subject) {
    echo "<li>" . htmlspecialchars($subject['subjectName']) . "<br>";
    if (!empty($subject['pictureFile'])) {
        echo "<img src='../../uploads/" . $subject['pictureFile'] . "' alt='Lecturer Picture' style='max-width: 100px; max-height: 100px;'><br>";
    }
    echo "<form action='' method='POST'>
              <input type='hidden' name='subject_id' value='" . $subject['id'] . "'>
              <input type='text' name='pin' placeholder='Enter PIN' required>
              <button type='submit'>View Messages</button>
          </form>
      </li>";
}

echo '</ul>';

// Handling PIN submission and displaying messages
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subject_id'], $_POST['pin'])) {
    $subjectId = $_POST['subject_id'];
    $enteredPIN = $_POST['pin'];

    // Verify the PIN
    $stmt = $pdo->prepare("SELECT id FROM subjects WHERE id = :subject_id AND subjectPIN = :pin");
    $stmt->bindParam(':subject_id', $subjectId);
    $stmt->bindParam(':pin', $enteredPIN);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Fetch messages for the subject
        $stmt = $pdo->prepare("SELECT m.id, m.message, m.answer, c.comment AS anonymous_comment
                               FROM messages m
                               LEFT JOIN comments c ON m.id = c.message_id
                               WHERE m.subject_id = :subject_id");
        $stmt->bindParam(':subject_id', $subjectId);
        $stmt->execute();
        $messages = $stmt->fetchAll();

        echo "<h2>Messages for Subject ID: " . htmlspecialchars($subjectId) . "</h2>";

        foreach ($messages as $message) {
            echo "<div class='message'>";
            echo "<p>Message: " . htmlspecialchars($message['message']) . "</p>";
            if (!empty($message['answer'])) {
                echo "<p>Professor reply: " . htmlspecialchars($message['answer']) . "</p>";
            }

            // Display anonymous comment
            if (!empty($message['anonymous_comment'])) {
                echo "<p>Anonymous comment: " . htmlspecialchars($message['anonymous_comment']) . "</p>";
            } else {
                // Show form to submit a comment
                echo "<form action='anonPostComment.php' method='POST'>";
                echo "<input type='hidden' name='message_id' value='" . $message['id'] . "'>";
                echo "<input type='text' name='anonymous_comment' placeholder='Add a comment'>";
                echo "<button type='submit'>Submit Comment</button>";
                echo "</form>";
            }

            // Form to report a message
            echo "<form action='submitReport.php' method='POST' style='margin-top: 10px;'>";
            echo "<input type='hidden' name='message_id' value='" . $message['id'] . "'>";
            echo "<input type='text' name='report_text' placeholder='Enter your report'>";
            echo "<button type='submit'>Report Message</button>";
            echo "</form>";
            echo "<hr>";
            echo "</div><br>";
        }

    } else {
        echo "<p>Invalid PIN for selected subject.</p>";
    }
}

echo '</body>
</html>';
?>
