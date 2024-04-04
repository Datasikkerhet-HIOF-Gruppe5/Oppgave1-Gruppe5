<?php
// Include database connection
include_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get post data
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Prepare SQL statement to fetch student data based on email
        $pdo = db_connect::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        // Fetch student data
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch subjects
        $stmt = $pdo->prepare("SELECT * FROM subjects");
        $stmt->execute();
        $subjects = $stmt->fetchAll();
    
        // Verify password
        if ($student && password_verify($password, $student['password'])) {
            // Password is correct
            $response = array(
                'status' => 'success',
                'message' => 'Login successful',
                'firstName' => $student['firstName'],
                'lastName' => $student['lastName'],
                'email' => $student['email'],
                'subjects' => $subjects
            );
        } else {
            // Invalid credentials
            $response = array(
                'status' => 'error',
                'message' => 'Invalid email or password'
            );
        }
    } catch (PDOException $e) {
        // Error occurred
        $response = array(
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        );
    } 

    echo json_encode($response);
    exit;
}
