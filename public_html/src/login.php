<?php

include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['failed_attempts'])) {
        $_SESSION['failed_attempts'] = 0;
        $_SESSION['last_attempt_time'] = time();
    }

    // Handle anonymous login
    if (isset($_POST['subject_id']) && !empty($_POST['subject_id'])) {
        $_SESSION['user_id'] = $_POST['subject_id'];
        $_SESSION['user_role'] = 'anonymous';
        header("Location: ../src/anonReadMsg.php");
        exit;
    }

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        echo "Invalid email format.";
        exit;
    }

    $current_time = time();
    $time_since_last_attempt = $current_time - $_SESSION['last_attempt_time'];
    $delay_seconds = calculateDelay($_SESSION['failed_attempts']); // You'll define this function

    if ($time_since_last_attempt < $delay_seconds) {
        $wait_time_seconds = $delay_seconds - $time_since_last_attempt;
        echo "Please wait " . round($wait_time_seconds / 60, 2) . " more minutes before trying again.";
        exit;
    }

    try {
        // Check if user is a student
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = 'student';
            echo "Student login successful.";
            // Redirect to a student-specific page or dashboard if needed
            header("Location: ../src/studReadMsg.php");
            exit;

        } else {
            // If not a student, check if user is a professor
            $stmt = $pdo->prepare("SELECT * FROM professors WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = 'professor';
                echo "Professor login successful.";
                // Redirect to a professor-specific page or dashboard if needed
                header("Location: ../src/profReadMsg.php");
                exit;
            } else {
                echo "Invalid credentials.";
            }
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo "Database error: " . $e->getMessage();
    }

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        header("Location: login.php");
        exit;
    }

    switch ($_SESSION['user_role']) {
        case 'student':
            header("Location: ../src/studReadMsg.php");
            break;
        case 'professor':
            header("Location: ../src/profReadMsg.php");
            break;
        case 'anonymous':
            header("Location: ../src/anonReadMsg.php");
            break;
        default:
            echo "Invalid user role.";
            break;
    }
    function calculateDelay($attempts) {
        $delays = [300, 600, 900, 1200, 2400, 3600, 10800, 21600, 43200]; // Delays in seconds
        return isset($delays[$attempts]) ? $delays[$attempts] : end($delays); // Use last value if attempts exceed delays array
    }
}

