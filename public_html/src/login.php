<?php

include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

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
}

