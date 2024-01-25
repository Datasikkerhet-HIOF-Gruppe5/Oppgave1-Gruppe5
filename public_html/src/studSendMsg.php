<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subjectId = $_POST['subject_id'];
    $messageText = $_POST['message_text'];
    $studentId = $_SESSION['user_id']; // Assuming the student is logged in

    $stmt = $pdo->prepare("INSERT INTO messages (subject_id, student_id, message_text) VALUES (?, ?, ?)");
    $stmt->execute([$subjectId, $studentId, $messageText]);

    echo "Message sent successfully.";
}
?>
