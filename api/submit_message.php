<?php
// Include database connection
include_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_POST['student_id'])) {
    $response = array(
        'status' => 'error',
        'message' => 'User not logged in'
    );
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get post data
    $student_id = $_POST['student_id'];
    $newMessage = $_POST['new_message'];
    $subjectId = $_POST['subject_id'];

    try {
        // Insert message into the database
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO messages (message, subject_id, student_id) VALUES (:message, :subject_id, :student_id)");
        $stmt->bindParam(':message', $newMessage);
        $stmt->bindParam(':subject_id', $subjectId);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();

        $response = array(
            'status' => 'success',
            'message' => 'Message sent successfully'
        );
        echo json_encode($response);
    } catch(PDOException $e) {
        $response = array(
            'status' => 'error',
            'message' => 'Error sending message: ' . $e->getMessage()
        );
        echo json_encode($response);
        exit;
    }
}

