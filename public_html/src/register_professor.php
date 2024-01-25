<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $subjectName = $_POST['subjectName'];
    $subjectCode = $_POST['subjectCode'];
    $pinCode = $_POST['pinCode'];

    // Handling file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["picture"]["name"]);
    move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);

    $stmt = $pdo->prepare("INSERT INTO professors (name, email, password, picture) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $target_file]);

    // Create a new subject
    $stmt = $pdo->prepare("INSERT INTO subjects (name, code, pin_code, professor_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$subjectName, $subjectCode, $pinCode, $pdo->lastInsertId()]);

    echo "Professor registered successfully.";
}
?>
