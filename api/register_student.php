<?php

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    function checkEmailExistence($email) {
        $pdo = Database::getInstance();
        $query = "SELECT * FROM students WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        return is_array($stmt->fetch(PDO::FETCH_ASSOC));
    }

    // Sanitize firstname
    $firstName = filter_var($_POST['firstName'], FILTER_SANITIZE_STRING);
    if (!preg_match("/^[a-zA-Z-' ]*$/", $firstName) || strlen($firstName) > 50) {
        die("Invalid name");
    }
    // Sanitize lastname
    $lastName = filter_var($_POST['lastName'], FILTER_SANITIZE_STRING);
    if (!preg_match("/^[a-zA-Z-' ]*$/", $lastName) || strlen($lastName) > 50) {
        die("Invalid name");
    }

    // Validate and sanitize email
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email || strlen($email) > 100) {
        die("Invalid email format");
    }
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Password hashing
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists

    $checkEmail = checkEmailExistence($email);

    if ($checkEmail) {
        $response = array(
            'status' => 'error',
            'message' => 'Registration error: Duplicate information'
        );
    }

    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO students (firstName, lastName, email, fieldOfStudy, classOf, password) VALUES (:firstName, :lastName, :email, :fieldOfStudy, :classOf, :password)");
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':fieldOfStudy', $fieldOfStudy);
        $stmt->bindParam(':classOf', $classOf);
        $stmt->bindParam(':password', $password);

        $stmt->execute();
        $response = array(
            'status' => 'success',
            'message' => 'Student registered successfully'
        );
    } catch(PDOException $e) {
        $response = array(
            'status' => 'error',
            'message' => 'Error registering student: ' . $e->getMessage()
        );
    } echo json_encode($response);
}
