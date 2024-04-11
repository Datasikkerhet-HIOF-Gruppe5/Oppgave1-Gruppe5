<?php

include 'db_connect.php';
include_once  '../../api/logger.php';
require_once  '../../api/init.php';

function checkEmailExistence($email): bool
{
    $pdo = Database::getInstance();

    $query = "SELECT * FROM professors WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if CSRF token is set and valid
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    // Sanitize name
    $firstName = filter_var($_POST['firstName'], FILTER_SANITIZE_STRING);
    if (!preg_match("/^[a-zA-Z-' ]*$/", $firstName) || strlen($firstName) > 50) {
        die("Invalid name");
    }

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
        die("Password must be at least 10 characters long");
    }
    if (!preg_match('/[A-Z]/', $password)) {
        die("Password must contain at least one uppercase letter");
    }
    if (!preg_match('/[a-z]/', $password)) {
        die("Password must contain at least one lowercase letter");
    }
    if (!preg_match('/[0-9]/', $password)) {
        die("Password must contain at least one digit");
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        die("Password must contain at least one special character");
    }

    // Password hashing
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = checkEmailExistence($email);

    if ($checkEmail) {
        die("Registration error: Duplicate information");
    }

    // Handling file upload
    $fileSize = $_FILES['picture']['size'];
    $pictureFile = NULL;

    if ($fileSize > 0 && $fileSize < 500000) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($pictureFile);

        if (in_array($mime, ['image/jpeg', 'image/png'])) {
            $pictureFile = uniqid('', true) . ".jpg";
            $fileDestination = '../../uploads/' . '/' . $pictureFile;

            if (move_uploaded_file($pictureFile, $fileDestination)) {
                // File upload successful
            } else {
                die("There was an error uploading your picture.");
            }
        } else {
            die("Invalid file type. Only JPG and PNG are allowed.");
        }
    } else {
        die("File size is either too large or missing.");
    }

    // Insert professor data into the database
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("INSERT INTO professors (firstName, lastName, email, password, pictureFile) 
    VALUES (:firstName, :lastName, :email, :password, :pictureFile)");

    $stmt->bindParam(':firstName', $firstName);
    $stmt->bindParam(':lastName', $lastName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password); // Assuming $password is already hashed
    $stmt->bindParam(':pictureFile', $pictureFile);

    if (!$stmt->execute()) {
        die("Registration failed.");
    }

    // Get the last inserted professor's ID
    $professorId = $pdo->lastInsertId();

    // Fetch and sanitize subjectName and subjectPIN from the form
    $subjectName = isset($_POST['subjectName']) ? filter_var($_POST['subjectName'], FILTER_SANITIZE_STRING) : '';
    $subjectPIN = isset($_POST['subjectPIN']) ? filter_var($_POST['subjectPIN'], FILTER_SANITIZE_STRING) : '';

    // Check if subjectName and subjectPIN are not empty
    if (!empty($subjectName) && !empty($subjectPIN)) {
        // Prepare the SQL statement for the subjects table
        $stmt = $pdo->prepare("INSERT INTO subjects (subjectName, subjectPIN, professor_id) VALUES (:subjectName, :subjectPIN, :professorId)");
        $stmt->bindParam(':subjectName', $subjectName);
        $stmt->bindParam(':subjectPIN', $subjectPIN);
        $stmt->bindParam(':professorId', $professorId);

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            writeToLog("Created a new user. Type: Professor.");
            echo "<p>Professor and subject registration successful.</p>";
            echo "<p>Redirecting back to login...</p>";
            header("Refresh:3; url=../index.php"); // Redirect to login.php after 3 seconds
        } else {
            die("Professor or subject registration failed.");
        }
    } else {
        die("Missing subject name or PIN.");
    }
}
?>
