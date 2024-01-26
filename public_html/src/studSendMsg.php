<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming subject_id is an integer
    $subjectId = filter_var($_POST['subject_id'], FILTER_VALIDATE_INT);
    if (!$subjectId) {
        die("Invalid subject ID");
    }

    // Sanitize message text
    $messageText = filter_var($_POST['message_text'], FILTER_SANITIZE_STRING);

    $studentId = $_SESSION['user_id']; // Assuming the student is logged in

    // Insert message data into the database
    $stmt = $pdo->prepare("INSERT INTO messages (subject_id, student_id, message_text) VALUES (:subject_id, :student_id, :message_text)");
    $stmt->bindParam(':subject_id', $subjectId);
    $stmt->bindParam(':student_id', $studentId);
    $stmt->bindParam(':message_text', $messageText);
    $stmt->execute();

    echo "Message sent successfully.";
}
?>
