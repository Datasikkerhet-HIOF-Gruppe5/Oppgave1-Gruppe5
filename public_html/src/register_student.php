<?php

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize firstname
    $firstName = filter_var($_POST['firstName'], FILTER_SANITIZE_STRING);
    if (!preg_match("/^[a-zA-Z-' ]*$/", $firstName) || strlen($firstName) > 50) {
        die("Invalid first name");
    }

    // Sanitize lastname
    $lastName = filter_var($_POST['lastName'], FILTER_SANITIZE_STRING);
    if (!preg_match("/^[a-zA-Z-' ]*$/", $lastName) || strlen($lastName) > 50) {
        die("Invalid last name");
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
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = checkEmailExistence($email);

    if ($checkEmail) {
        die("Registration error: Duplicate information");
    }

    // Insert student data into the database
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("INSERT INTO students (firstName, lastName, email, password) VALUES (:firstName, :lastName, :email, :password)");
    $stmt->bindParam(':firstName', $firstName);
    $stmt->bindParam(':lastName', $lastName);
    $stmt->bindParam(':email', $email);
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
?>
