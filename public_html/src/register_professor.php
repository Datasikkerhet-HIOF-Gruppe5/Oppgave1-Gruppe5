<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize name
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

    // Validate and sanitize email
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        die("Invalid email format");
    }
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $subjectName = $_POST['subjectName'];
    $subjectCode = $_POST['subjectCode'];
    $pinCode = $_POST['pinCode'];

    // Check if email already exists
    $checkEmail = checkEmailExistence($email);
    if ($checkEmail) {
        die("That email already exists");
    }

    // Check if name already exists
    $checkName = checkNameExistence($name);
    if ($checkName) {
        die("That name already exists");
    }

    // Handling file upload
    $target_dir = "uploads/";
    $picture = $_FILES["picture"]["name"];
    $target_file = $target_dir . basename($picture);
    move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);

    // Insert professor data into the database
    $stmt = $pdo->prepare("INSERT INTO professors (name, email, password, picture) VALUES (:name, :email, :password, :picture)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':picture', $target_file);
    $stmt->execute();

    // Get the last inserted professor ID to use for the subject
    $professorId = $pdo->lastInsertId();

    // Create a new subject
    $stmt = $pdo->prepare("INSERT INTO subjects (name, code, pin_code, professor_id) VALUES (:name, :code, :pin_code, :professor_id)");
    $stmt->bindParam(':name', $subjectName);
    $stmt->bindParam(':code', $subjectCode);
    $stmt->bindParam(':pin_code', $pinCode);
    $stmt->bindParam(':professor_id', $professorId);
    $stmt->execute();

    echo "Professor registered successfully.";
}

function checkEmailExistence($email) {
    global $pdo;

    $query = "SELECT * FROM professors WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return is_array($stmt->fetch(PDO::FETCH_ASSOC));
}

function checkNameExistence($name) {
    global $pdo;

    $query = "SELECT * FROM professors WHERE name = :name LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->execute();

    return ($stmt->rowCount() > 0);
}
?>
