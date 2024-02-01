<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $pinCode = $_GET['pin']; // Assuming a GET request with the PIN code

    // Fetch messages and replies based on the professor's PIN code
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("SELECT m.id, m.message_text, s.code AS subject_code, s.name AS subject_name, p.name AS professor_name, p.picture 
                           FROM messages m 
                           JOIN subjects s ON m.subject_id = s.id 
                           JOIN professors p ON s.professor_id = p.id 
                           WHERE s.pin_code = :pin_code");
    $stmt->bindParam(':pin_code', $pinCode);
    $stmt->execute();
    $messages = $stmt->fetchAll();

    foreach ($messages as $message) {
        echo "Subject: " . $message['subject_name'] . " (" . $message['subject_code'] . ")<br>";
        echo "Professor: " . $message['professor_name'] . "<br>";
        echo "<img src='" . $message['picture'] . "' alt='Professor Picture'><br>";
        echo "Message: " . $message['message_text'] . "<br><br>";
    }
}

