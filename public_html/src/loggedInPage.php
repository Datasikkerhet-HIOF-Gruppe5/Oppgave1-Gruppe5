<?php
session_start();

include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

if ($_SESSION['user_role'] == 'student') {
    // Fetch and display subjects for students
    $stmt = $pdo->prepare("SELECT * FROM subjects");
    $stmt->execute();
    $subjects = $stmt->fetchAll();

    foreach ($subjects as $subject) {
        echo "<a href='send_message.php?subject_id=" . $subject['id'] . "'>" . htmlspecialchars($subject['name']) . " (" . htmlspecialchars($subject['code']) . ")</a><br>";
    }
} elseif ($_SESSION['user_role'] == 'professor') {
    // Fetch and display messages for the professor
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE professor_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $messages = $stmt->fetchAll();

    echo "<h2>Your Messages</h2>";
    foreach ($messages as $message) {
        echo "<p>" . htmlspecialchars($message['content']) . "</p>"; // Assuming 'content' is a column in your messages table
    }
} else {
    echo "Invalid user role.";
}
?>
