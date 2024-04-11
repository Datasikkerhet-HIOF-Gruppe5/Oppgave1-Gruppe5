<?php

include 'db_connect.php';
include_once  '../../api/logger.php';
require_once  '../../api/init.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if CSRF token is set and valid
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
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

    // Password rules
    $password = $_POST['password'];
    if (strlen($password) < 10) {
        throw new Exception("Password must be at least 10 characters long");
    }
    if (!preg_match('/[A-Z]/', $password)) {
        throw new Exception("Password must contain at least one uppercase letter");
    }
    if (!preg_match('/[a-z]/', $password)) {
        throw new Exception("Password must contain at least one lowercase letter");
    }
    if (!preg_match('/[0-9]/', $password)) {
        throw new Exception("Password must contain at least one digit");
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        throw new Exception("Password must contain at least one special character");
    }

    // Password hashing
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = checkEmailExistence($email);

    if ($checkEmail) {
        die("Registration error: Duplicate information");
    }

// Insert student data into the database
    $pdo = db_connect::getInstance();
    $stmt = $pdo->prepare("INSERT INTO students (firstName, lastName, email, fieldOfStudy, classOf, password) VALUES (:firstName, :lastName, :email, :fieldOfStudy, :classOf, :password)");
    $stmt->bindParam(':firstName', $firstName);
    $stmt->bindParam(':lastName', $lastName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':fieldOfStudy', $fieldOfStudy);
    $stmt->bindParam(':classOf', $classOf);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        writeToLog("Created a new user. Type: Student.");
        echo "<p>Registration successful.</p>";
        echo "<p>Redirecting back to login...</p>";
        header("Refresh:3; url=../index.php"); // Redirect to login.php after 3 seconds
    } else {
        echo "<p>Registration failed.</p>";
    }
}

function checkEmailExistence($email): bool
{
    $pdo = db_connect::getInstance();
    $query = "SELECT * FROM students WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return is_array($stmt->fetch(PDO::FETCH_ASSOC));
}

