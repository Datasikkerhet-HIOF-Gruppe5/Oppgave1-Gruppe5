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

    // Password hashing
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

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

    // Insert student data into the database
    $stmt = $pdo->prepare("INSERT INTO students (name, email, password) VALUES (:name, :email, :password)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    echo "Student registered successfully.";
}

function checkEmailExistence($email) {
    global $pdo;

    $query = "SELECT * FROM students WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return is_array($stmt->fetch(PDO::FETCH_ASSOC));
}

function checkNameExistence($name) {
    global $pdo;

    $query = "SELECT * FROM students WHERE name = :name LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->execute();

    return ($stmt->rowCount() > 0);
}
?>
