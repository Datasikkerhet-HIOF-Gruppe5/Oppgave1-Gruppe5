<?php

include 'db_connect.php';

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

    $feildOfStudy = filter_var($_POST['feildOfStudy'], FILTER_SANITIZE_STRING);
    if (!preg_match("/^[a-zA-Z-' ]*$/", $feildOfStudy) || strlen($feildOfStudy) > 50) {
        die("Invalid name");
    }

    $classOf = filter_var($_POST['classOf'], FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($classOf, FILTER_VALIDATE_INT) || $classOf < 2000) {
        die("Invalid classOf year");
    }


    // Password hashing
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = checkEmailExistence($email);

    if ($checkEmail) {
        die("Registration error: Duplicate information");
    }

    // Handling file upload
    $target_dir = "uploads/";
    $picture = $_FILES["picture"]["name"];
    $target_file = $target_dir . basename($picture);
    move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);

    // Insert professor data into the database
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("INSERT INTO professors (firstName, lastName, email, feildOfStudy, classOf, password) VALUES (:firstName, :lastName, :email, :feildOfStudy, :classOf, :password)");

    $stmt->bindParam(':firstName', $firstName);
    $stmt->bindParam(':lastName', $lastName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':feildOfStudy', $feildOfStudy);
    $stmt->bindParam(':classOf', $classOf, PDO::PARAM_INT);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo "<p>Registration successful.</p>";
        echo "<p>Redirecting back to login...</p>";
        header("Refresh:3; url=../index.html"); // Redirect to login.php after 3 seconds
    } else {
        echo "<p>Registration failed.</p>";
    }
}

function checkEmailExistence($email) {
    $pdo = Database::getInstance();
    $query = "SELECT * FROM students WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return is_array($stmt->fetch(PDO::FETCH_ASSOC));
}

