<?php

include 'db_connect.php';

function checkEmailExistence($email) {
    $pdo = Database::getInstance();

    $query = "SELECT * FROM professors WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    if ($fileSize !== 0) {
        $fileName = $_FILES['picture']['name'];
        $fileTmpName = $_FILES['picture']['tmp_name'];
        $fileError = $_FILES['picture']['error'];
    
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
    
        $allowed = array('jpg', 'jpeg', 'png');
    
        if(in_array($fileActualExt, $allowed)) {
            if($fileError === 0 && $fileSize < 500000) {
                    $pictureFile = uniqid('', true) . ".jpg";
    
                    $fileDestination = '../../uploads/' . $pictureFile;
                move_uploaded_file($fileTmpName, $fileDestination);
            } 
        } else {
            echo 'There was an error uploading your picture';
        }
    }

// Insert professor data into the first database
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("INSERT INTO professors (firstName, lastName, email, password, pictureFile) 
    VALUES (:firstName, :lastName, :email, :password, :pictureFile)");

    $stmt->bindParam(':firstName', $firstName);
    $stmt->bindParam(':lastName', $lastName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password); // Assuming $password is already hashed
    $stmt->bindParam(':pictureFile', $pictureFile);

    if (!$stmt->execute()) {
        echo "<p>Registration failed.</p>";
        // Handle error, possibly exit script
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
            echo "<p>Professor and subject registration successful.</p>";
            echo "<p>Redirecting back to login...</p>";
            header("Refresh:3; url=../index.html"); // Redirect to login.php after 3 seconds
        } else {
            echo "<p>Professor or subject registration failed.</p>";
        }
    } else {
        echo "<p>Missing subject name or PIN.</p>";
    }
}

