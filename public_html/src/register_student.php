<?php

include 'db_connect.php';
include_once  '../../api/logger.php';
require_once  '../../api/init.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if CSRF token is set and valid
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    try {
        // Sanitize firstname
        $firstName = filter_var($_POST['firstName'], FILTER_SANITIZE_STRING);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $firstName) || strlen($firstName) > 50) {
            throw new Exception("Invalid first name");
        }
        // Sanitize lastname
        $lastName = filter_var($_POST['lastName'], FILTER_SANITIZE_STRING);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $lastName) || strlen($lastName) > 50) {
            throw new Exception("Invalid last name");
        }

        // Validate and sanitize email
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if (!$email || strlen($email) > 100) {
            throw new Exception("Invalid email format");
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
            throw new Exception("Registration error: Duplicate information");
        }

        // Insert student data into the database
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO students (firstName, lastName, email, password) VALUES (:firstName, :lastName, :email, :password)");
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            writeToLog("Created a new user. Type: Student.");
            echo "<p>Registration successful.</p>";
            echo "<p>Redirecting back to login...</p>";
            header("Refresh:3; url=../index.php"); // Redirect to index.html after 3 seconds
        } else {
            throw new Exception("<p>Registration failed.</p>");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function checkEmailExistence($email): bool
{
    $pdo = Database::getInstance();
    $query = "SELECT * FROM students WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return is_array($stmt->fetch(PDO::FETCH_ASSOC));
}
?>
